import dotenv from 'dotenv';
import pg from 'pg';
import axios from 'axios';

dotenv.config();

const pool = new pg.Pool({
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT || '5432'),
    database: process.env.DB_NAME || 'frost-devel',
    user: process.env.DB_USER || 'postgres',
    password: process.env.DB_PASSWORD || ''
});

const SERVICE_URL = `http://localhost:${process.env.PORT || 3003}`;

async function testWithExistingData() {
    console.log('🔍 Fetching existing chat data from database...\n');

    try {
        // Get recent chat messages
        const query = `
            SELECT
                cl.id,
                cl.body,
                cl.student_id,
                cl.course_date_id,
                cl.created_at,
                u.fname || ' ' || u.lname as student_name
            FROM chat_logs cl
            LEFT JOIN users u ON cl.student_id = u.id
            WHERE cl.hidden_at IS NULL
            AND cl.created_at > NOW() - INTERVAL '7 days'
            ORDER BY cl.created_at DESC
            LIMIT 20
        `;

        const result = await pool.query(query);
        const messages = result.rows;

        console.log(`✅ Found ${messages.length} messages from the last 7 days\n`);

        if (messages.length === 0) {
            console.log('❌ No chat messages found. Make sure you have chat data in the database.');
            return;
        }

        // Display messages
        console.log('📋 Recent Messages:');
        console.log('─'.repeat(80));
        messages.forEach((msg, idx) => {
            console.log(`\n${idx + 1}. [${msg.created_at.toLocaleString()}]`);
            console.log(`   Student: ${msg.student_name || 'Unknown'} (ID: ${msg.student_id})`);
            console.log(`   Message: "${msg.body}"`);
        });
        console.log('\n' + '─'.repeat(80));

        // Test analysis with first few messages
        console.log('\n🤖 Testing AI analysis on sample messages...\n');

        for (let i = 0; i < Math.min(5, messages.length); i++) {
            const msg = messages[i];

            try {
                const response = await axios.post(`${SERVICE_URL}/api/analyze-message`, {
                    message_id: msg.id,
                    text: msg.body,
                    student_id: msg.student_id,
                    course_date_id: msg.course_date_id
                });

                console.log(`\n📊 Analysis for message ${i + 1}:`);
                console.log(`   Text: "${msg.body}"`);
                console.log(`   Sentiment: ${response.data.sentiment} (${response.data.sentiment_score})`);
                console.log(`   Indicators: ${response.data.indicators.join(', ') || 'none'}`);
                console.log(`   Urgency: ${response.data.urgency}`);
            } catch (err) {
                console.error(`   ❌ Analysis failed:`, err.message);
            }
        }

        console.log('\n✅ Test complete!\n');

        // Get session stats
        const sessionQuery = `
            SELECT
                course_date_id,
                COUNT(*) as message_count,
                COUNT(DISTINCT student_id) as student_count,
                MIN(created_at) as first_message,
                MAX(created_at) as last_message
            FROM chat_logs
            WHERE hidden_at IS NULL
            AND created_at > NOW() - INTERVAL '24 hours'
            GROUP BY course_date_id
            ORDER BY message_count DESC
            LIMIT 5
        `;

        const sessions = await pool.query(sessionQuery);

        if (sessions.rows.length > 0) {
            console.log('\n📊 Active Sessions (Last 24 hours):');
            console.log('─'.repeat(80));
            sessions.rows.forEach(session => {
                console.log(`\n   Course Date ID: ${session.course_date_id}`);
                console.log(`   Messages: ${session.message_count}`);
                console.log(`   Students: ${session.student_count}`);
                console.log(`   Duration: ${session.first_message.toLocaleString()} - ${session.last_message.toLocaleString()}`);
            });
            console.log('\n' + '─'.repeat(80));
        }

    } catch (err) {
        console.error('❌ Error:', err.message);
        console.error(err.stack);
    } finally {
        await pool.end();
    }
}

// Run the test
console.log('🚀 AI Course Assistant - Testing with Existing Data\n');
console.log(`Connecting to: ${process.env.DB_NAME}@${process.env.DB_HOST}\n`);

testWithExistingData();
