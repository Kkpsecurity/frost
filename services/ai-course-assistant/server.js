import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import pg from 'pg';
import axios from 'axios';
import { WebSocketServer } from 'ws';

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3003;
const WS_PORT = process.env.WS_PORT || 3004;

// Middleware
app.use(cors({
    origin: process.env.ALLOWED_ORIGINS?.split(',') || '*'
}));
app.use(express.json());

// Database connection pool
const pool = new pg.Pool({
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT || '5432'),
    database: process.env.DB_NAME || 'frost-devel',
    user: process.env.DB_USER || 'postgres',
    password: process.env.DB_PASSWORD || ''
});

// Test database connection
pool.connect((err, client, release) => {
    if (err) {
        console.error('❌ Database connection error:', err.stack);
    } else {
        console.log('✅ Connected to PostgreSQL database');
        release();
    }
});

// AI Provider configuration
const AI_PROVIDER = process.env.AI_PROVIDER || 'llama';
const LLAMA_HOST = process.env.LLAMA_HOST || 'http://localhost:11434';
const MODEL_NAME = process.env.MODEL_NAME || 'llama2';

console.log(`🤖 AI Provider: ${AI_PROVIDER}`);
console.log(`📦 Model: ${MODEL_NAME}`);

// WebSocket Server for real-time updates
const wss = new WebSocketServer({ port: WS_PORT });
const subscriptions = new Map(); // course_date_id -> Set of WebSocket clients

wss.on('connection', (ws) => {
    console.log('🔌 WebSocket client connected');

    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message.toString());

            if (data.action === 'subscribe' && data.course_date_id) {
                if (!subscriptions.has(data.course_date_id)) {
                    subscriptions.set(data.course_date_id, new Set());
                }
                subscriptions.get(data.course_date_id).add(ws);
                console.log(`📡 Client subscribed to course_date_id: ${data.course_date_id}`);
            }

            if (data.action === 'unsubscribe' && data.course_date_id) {
                subscriptions.get(data.course_date_id)?.delete(ws);
            }
        } catch (err) {
            console.error('WebSocket message error:', err);
        }
    });

    ws.on('close', () => {
        // Remove from all subscriptions
        subscriptions.forEach((clients) => clients.delete(ws));
        console.log('🔌 WebSocket client disconnected');
    });
});

// Broadcast to all subscribers of a course_date_id
function broadcast(course_date_id, data) {
    const clients = subscriptions.get(course_date_id);
    if (clients) {
        clients.forEach((client) => {
            if (client.readyState === 1) { // OPEN
                client.send(JSON.stringify(data));
            }
        });
    }
}

// ==================== API ROUTES ====================

async function getLlmStatus() {
    if (AI_PROVIDER === 'llama') {
        const tagsUrl = new URL('/api/tags', LLAMA_HOST).toString();
        try {
            const response = await axios.get(tagsUrl, { timeout: 2500 });
            const models = Array.isArray(response.data?.models)
                ? response.data.models.map((m) => m?.name).filter(Boolean)
                : [];

            const hasRequestedModel = models.some((name) =>
                String(name).toLowerCase().startsWith(String(MODEL_NAME).toLowerCase()),
            );

            return {
                ok: true,
                provider: 'llama',
                host: LLAMA_HOST,
                model: MODEL_NAME,
                hasRequestedModel,
                models: models.slice(0, 25),
            };
        } catch (err) {
            const message =
                err?.code === 'ECONNABORTED'
                    ? 'Timed out connecting to Ollama'
                    : err?.message || 'Failed to connect to Ollama';
            return {
                ok: false,
                provider: 'llama',
                host: LLAMA_HOST,
                model: MODEL_NAME,
                error: message,
                hint:
                    'Install/run Ollama and ensure it is listening on LLAMA_HOST (default http://localhost:11434).',
            };
        }
    }

    if (AI_PROVIDER === 'openai') {
        const hasKey = Boolean(process.env.OPENAI_API_KEY);
        return {
            ok: hasKey,
            provider: 'openai',
            model: process.env.OPENAI_MODEL || MODEL_NAME,
            error: hasKey ? undefined : 'Missing OPENAI_API_KEY',
        };
    }

    return {
        ok: false,
        provider: AI_PROVIDER,
        error: `Unsupported AI_PROVIDER: ${AI_PROVIDER}`,
    };
}

// Health check
app.get('/health', async (req, res) => {
    const llm = await getLlmStatus();

    res.json({
        status: 'ok',
        service: 'frost-ai-course-assistant',
        ai_provider: AI_PROVIDER,
        model: MODEL_NAME,
        llm,
        timestamp: new Date().toISOString(),
    });
});

// Answer a course-related question
app.post('/api/ask-question', async (req, res) => {
    try {
        const { question, course_date_id, student_id, instructor_id, context } = req.body;

        if (!question) {
            return res.status(400).json({ error: 'Question text is required' });
        }

        console.log(`❓ Question from ${student_id ? 'student' : 'instructor'}: "${question}"`);

        // Get course context (lessons, materials)
        const courseContext = await getCourseContext(course_date_id);

        // Generate AI answer
        const answer = await answerQuestionWithAI(question, courseContext);

        // Store in ai_chat_logs
        await storeAIResponse(question, answer, student_id || instructor_id, context);

        res.json({
            question,
            answer: answer.response,
            decision: answer.decision,
            sources: answer.sources,
            timestamp: new Date().toISOString()
        });
    } catch (err) {
        console.error('Q&A error:', err);
        res.status(500).json({
            error: 'Failed to answer question',
            message: err.message,
            decision: 'error'
        });
    }
});

// Generate AI introduction message
app.post('/api/introduce', async (req, res) => {
    try {
        const { course_date_id } = req.body;

        if (!course_date_id) {
            return res.status(400).json({ error: 'course_date_id is required' });
        }

        console.log(`👋 Generating AI introduction for course_date_id: ${course_date_id}`);

        // Get course context
        const courseContext = await getCourseContext(course_date_id);

        // Generate introduction using LLM
        const introduction = await generateIntroduction(courseContext);

        res.json({
            introduction: introduction,
            timestamp: new Date().toISOString()
        });
    } catch (err) {
        console.error('Introduction generation error:', err);
        res.status(500).json({
            error: 'Failed to generate introduction',
            message: err.message
        });
    }
});

// Analyze entire chat session
app.post('/api/analyze-session', async (req, res) => {
    try {
        const { course_date_id, start_time, end_time } = req.body;

        if (!course_date_id) {
            return res.status(400).json({ error: 'course_date_id is required' });
        }

        console.log(`📊 Analyzing session for course_date_id: ${course_date_id}`);

        // Fetch messages from database
        const messages = await getSessionMessages(course_date_id, start_time, end_time);

        // Analyze all messages
        const insights = await analyzeSession(messages);

        res.json({
            course_date_id,
            message_count: messages.length,
            ...insights
        });
    } catch (err) {
        console.error('Session analysis error:', err);
        res.status(500).json({ error: 'Session analysis failed', message: err.message });
    }
});

// Get real-time insights for active session
app.get('/api/session-insights/:course_date_id', async (req, res) => {
    try {
        const { course_date_id } = req.params;

        // Get recent messages from chat_logs (existing table)
        const query = `
            SELECT
                cl.id,
                cl.body,
                cl.student_id,
                cl.inst_id,
                cl.created_at,
                u.fname || ' ' || u.lname as student_name
            FROM chat_logs cl
            LEFT JOIN users u ON cl.student_id = u.id
            WHERE cl.course_date_id = $1
            AND cl.hidden_at IS NULL
            AND cl.created_at > NOW() - INTERVAL '1 hour'
            ORDER BY cl.created_at DESC
            LIMIT 100
        `;

        const result = await pool.query(query, [course_date_id]);

        // Calculate real-time insights from messages
        const insights = calculateInsightsFromMessages(result.rows);

        return res.json({
            course_date_id: Number(course_date_id),
            ...insights,
        });
    } catch (err) {
        console.error('Session insights error:', err);
        return res.status(500).json({
            error: 'Failed to fetch session insights',
            message: err?.message,
        });
    }
});

// ==================== AI Q&A FUNCTIONS ====================

async function generateIntroduction(courseContext) {
    const courseName = courseContext.course_name || 'this course';

    // Load system prompts
    const fs = await import('fs');
    const path = await import('path');
    const __dirname = path.dirname(new URL(import.meta.url).pathname);

    let systemPrompt = '';
    try {
        const baseInstructions = fs.readFileSync(path.join(__dirname, 'knowledge', 'system-prompts', 'base-instructions.txt'), 'utf-8');
        systemPrompt = baseInstructions;
    } catch (err) {
        console.warn('Could not load system prompts:', err.message);
    }

    const prompt = `${systemPrompt}\n\nYou have just been enabled as the AI Course Assistant for ${courseName}. Please introduce yourself to the class in a friendly, professional manner. Keep it brief (2-3 sentences) and mention that you're here to help with course-related questions.`;

    // Use LLM to generate introduction
    if (AI_PROVIDER === 'llama') {
        try {
            const response = await axios.post(`${LLAMA_HOST}/api/generate`, {
                model: MODEL_NAME,
                prompt: prompt,
                stream: false,
                options: {
                    temperature: 0.7,
                    max_tokens: 150
                }
            }, { timeout: 10000 });

            const introduction = response.data?.response?.trim() ||
                               '👋 Hello! I\'m your AI Course Assistant. I\'m here to help answer questions about the course material. Feel free to ask me anything!';

            console.log('✅ LLM generated introduction');
            return introduction;
        } catch (err) {
            console.error('LLM introduction error:', err.message);
            // Fallback to template
            return `👋 Hello everyone! I'm your AI Course Assistant for ${courseName}. I'm here to help answer questions about what we're learning today. Feel free to ask me anything related to the course material!`;
        }
    }

    // Fallback for other providers or if LLM fails
    return `👋 Hello! I'm your AI Course Assistant. I'm here to help answer questions about ${courseName}. Feel free to ask me anything!`;
}

async function answerQuestionWithAI(question, courseContext) {
    // TODO: Integrate with actual AI model (LLaMA/OpenAI)
    // For now, return mock answer

    const lowerQuestion = question.toLowerCase();

    // Simple keyword-based responses (placeholder until LLaMA is integrated)
    if (lowerQuestion.includes('array') || lowerQuestion.includes('list')) {
        return {
            response: 'An array is a data structure that stores multiple values in a single variable. In most programming languages, arrays use index numbers to access elements, starting from 0.',
            decision: 'answered',
            sources: courseContext.lessons || []
        };
    }

    if (lowerQuestion.includes('loop') || lowerQuestion.includes('for') || lowerQuestion.includes('while')) {
        return {
            response: 'Loops allow you to repeat code multiple times. Common types include: for loops (when you know how many times), while loops (when condition-based), and forEach (for arrays).',
            decision: 'answered',
            sources: courseContext.lessons || []
        };
    }

    // Default response
    return {
        response: `I understand you're asking about: "${question}". Based on the course materials, I can help explain this concept. [AI integration pending - LLaMA will provide detailed answers]`,
        decision: 'answered',
        sources: courseContext.lessons || []
    };
}

async function analyzeSession(messages) {
    // Aggregate analysis across all messages
    const sentiments = messages.map(m => m.sentiment || 'neutral');
    const positive = sentiments.filter(s => s === 'positive').length;
    const negative = sentiments.filter(s => s === 'negative').length;

    const overall_sentiment = positive > negative ? 'positive' :
                             negative > positive ? 'negative' : 'neutral';

    return {
        overall_sentiment,
        engagement_score: Math.min(1.0, messages.length / 50),
        common_topics: [], // TODO: Extract with AI
        alerts: [],
        summary: 'Session analysis complete'
    };
}

// ==================== DATABASE FUNCTIONS ====================

async function getCourseContext(course_date_id) {
    try {
        // Get course information and lesson context
        const query = `
            SELECT
                cd.id as course_date_id,
                c.name as course_name,
                cu.day_number,
                l.id as lesson_id,
                l.name as lesson_name,
                l.description as lesson_description
            FROM course_dates cd
            JOIN course_auths ca ON cd.course_auth_id = ca.id
            JOIN courses c ON ca.course_id = c.id
            LEFT JOIN course_units cu ON cd.course_unit_id = cu.id
            LEFT JOIN lessons l ON cu.id = l.course_unit_id
            WHERE cd.id = $1
        `;

        const result = await pool.query(query, [course_date_id]);

        return {
            course_name: result.rows[0]?.course_name || 'Unknown',
            day_number: result.rows[0]?.day_number || 1,
            lessons: result.rows.map(r => ({
                id: r.lesson_id,
                name: r.lesson_name,
                description: r.lesson_description
            }))
        };
    } catch (err) {
        console.error('Error fetching course context:', err);
        return { lessons: [] };
    }
}

async function storeAIResponse(question, answer, requested_by, context) {
    try {
        const query = `
            INSERT INTO ai_chat_logs (
                instructor_question_id,
                requested_by,
                prompt,
                response,
                sources,
                decision
            )
            VALUES ($1, $2, $3, $4, $5, $6)
            RETURNING id
        `;

        const values = [
            context?.instructor_question_id || 0,
            requested_by,
            question,
            { text: answer.response },
            answer.sources || [],
            answer.decision
        ];

        const result = await pool.query(query, values);
        console.log(`📝 AI response stored with ID: ${result.rows[0].id}`);
    } catch (err) {
        console.error('Error storing AI response:', err);
    }
}

async function getSessionMessages(course_date_id, start_time, end_time) {
    const query = `
        SELECT
            cl.id,
            cl.body as text,
            cl.student_id,
            cl.inst_id,
            cl.created_at,
            u.fname || ' ' || u.lname as student_name
        FROM chat_logs cl
        LEFT JOIN users u ON cl.student_id = u.id
        WHERE cl.course_date_id = $1
        AND cl.hidden_at IS NULL
    `;

    const params = [course_date_id];
    if (start_time) params.push(start_time);
    if (end_time) params.push(end_time);

    const result = await pool.query(query, params);
    return result.rows;
}

function calculateInsights(analysisResults) {
    if (analysisResults.length === 0) {
        return {
            overall_sentiment: 'neutral',
            engagement_level: 'low',
            alerts: []
        };
    }

    // Aggregate sentiment
    const sentiments = analysisResults.map(r => r.sentiment);
    const positive = sentiments.filter(s => s === 'positive').length;
    const negative = sentiments.filter(s => s === 'negative').length;

    return {
        overall_sentiment: positive > negative ? 'positive' :
                          negative > positive ? 'negative' : 'neutral',
        total_messages: analysisResults.length,
        positive_count: positive,
        negative_count: negative,
        engagement_level: analysisResults.length > 20 ? 'high' : 'medium',
        alerts: []
    };
}

function calculateInsightsFromMessages(messages) {
    if (messages.length === 0) {
        return {
            overall_sentiment: 'neutral',
            engagement_level: 'low',
            message_count: 0,
            student_count: 0,
            alerts: []
        };
    }

    // Analyze messages directly
    const confusionKeywords = (process.env.CONFUSION_KEYWORDS || '').split(',');
    let confusionCount = 0;

    messages.forEach(msg => {
        const lowerText = msg.body.toLowerCase();
        if (confusionKeywords.some(kw => lowerText.includes(kw.trim()))) {
            confusionCount++;
        }
    });

    const uniqueStudents = new Set(messages.filter(m => m.student_id).map(m => m.student_id));

    return {
        overall_sentiment: confusionCount > messages.length * 0.3 ? 'negative' : 'neutral',
        engagement_level: messages.length > 20 ? 'high' : messages.length > 10 ? 'medium' : 'low',
        message_count: messages.length,
        student_count: uniqueStudents.size,
        confusion_detected: confusionCount > 0,
        confusion_count: confusionCount,
        alerts: confusionCount > 3 ? ['Multiple students showing confusion'] : []
    };
}

// ==================== START SERVER ====================

process.on('unhandledRejection', (reason) => {
    console.error('Unhandled promise rejection:', reason);
});

process.on('uncaughtException', (err) => {
    console.error('Uncaught exception:', err);
});

app.listen(PORT, () => {
    console.log(`✅ AI Course Assistant HTTP listening on :${PORT}`);
    console.log(`✅ WebSocket listening on :${WS_PORT}`);
});
