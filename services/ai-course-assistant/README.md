# Frost AI Course Assistant Service

AI-powered Q&A assistant for Frost Classroom using LLaMA (or similar LLM).

## Overview

This service provides an AI teaching assistant that answers course-related questions:

- Answer student questions about course content
- Explain programming concepts and syntax
- Reference course materials and lessons for context
- Provide code examples and explanations
- Store Q&A history in ai_chat_logs table
- Help instructors by handling common questions

**Important:** This is NOT chat monitoring or sentiment analysis. This is an AI tutor that helps answer course-related questions.

## Architecture

### Components

1. **Express Server** - REST API for Q&A requests
2. **LLaMA Integration** - AI model for answering questions
3. **PostgreSQL Client** - Database connection for course content
4. **Course Context Engine** - Retrieves relevant lesson materials

### Integration Points

- **Frontend**: ChatPanel.tsx (instructor) - AI toggle button enables AI assistant
- **Backend**: Laravel API endpoints for Q&A requests
- **Database**: `ai_chat_logs` table (existing), course materials for context
- **AI Model**: LLaMA (local) or OpenAI API (cloud)

## Setup

### Prerequisites

- Node.js 18+
- PostgreSQL (Frost database)
- LLaMA model (local) OR OpenAI API key (cloud)

### Stable Local LLM (Recommended): Ollama on Windows

1. Install Ollama for Windows: https://ollama.com/download
2. Open a new terminal and verify it works:

```bash
ollama --version
```

3. Pull a model (pick one and stick to it for stability). Examples:

```bash
ollama pull llama3.1:8b
```

4. Confirm the HTTP API is reachable (default is port 11434):

```bash
curl http://localhost:11434/api/tags
```

5. Set your `.env` values:

```env
AI_PROVIDER=llama
LLAMA_HOST=http://localhost:11434
MODEL_NAME=llama3.1:8b
```

6. Start this service and confirm it can see Ollama:

```bash
npm run dev
curl http://localhost:3003/health
```

The `/health` response includes an `llm` object showing whether Ollama is reachable and whether the requested model is present.

### Installation

```bash
cd services/ai-course-assistant
npm install
```

### Configuration

Create `.env` file:

```env
# Service Configuration
PORT=3003
NODE_ENV=development

# Database (PostgreSQL - Frost)
DB_HOST=localhost
DB_PORT=5432
DB_NAME=frost-devel
DB_USER=postgres
DB_PASSWORD=your_password

# AI Model Configuration
AI_PROVIDER=llama  # Options: llama, openai, anthropic
LLAMA_HOST=http://localhost:11434  # Ollama server
OPENAI_API_KEY=your_api_key_here  # If using OpenAI
MODEL_NAME=llama2  # or gpt-4, gpt-3.5-turbo

# Analysis Settings
ANALYSIS_INTERVAL=5000  # ms - how often to batch analyze messages
SENTIMENT_THRESHOLD=0.3  # negative sentiment alert threshold
CONFUSION_KEYWORDS=help,confused,stuck,don't understand,lost
MAX_BATCH_SIZE=50  # max messages to analyze at once

# WebSocket
WS_PORT=3004

# Logging
LOG_LEVEL=info
```

## Usage

### Development

```bash
npm run dev
```

### Production

```bash
npm start
```

## API Endpoints

### POST /api/analyze-message

Analyze a single chat message

**Request:**

```json
{
    "message_id": 123,
    "text": "I'm really confused about this topic",
    "student_id": 456,
    "course_date_id": 789
}
```

**Response:**

```json
{
    "message_id": 123,
    "sentiment": "negative",
    "sentiment_score": -0.65,
    "indicators": ["confusion", "frustration"],
    "urgency": "medium",
    "topics": ["lesson_content"],
    "suggested_response": "Consider pausing to review the current concept"
}
```

### POST /api/analyze-session

Analyze entire chat session

**Request:**

```json
{
    "course_date_id": 789,
    "start_time": "2026-02-03T10:00:00Z",
    "end_time": "2026-02-03T12:00:00Z"
}
```

**Response:**

```json
{
    "course_date_id": 789,
    "message_count": 45,
    "overall_sentiment": "positive",
    "engagement_score": 0.78,
    "common_topics": ["arrays", "loops", "syntax"],
    "alerts": [
        {
            "type": "confusion",
            "count": 3,
            "students": [123, 456, 789]
        }
    ],
    "summary": "Students are engaged but showing confusion around loop syntax"
}
```

### GET /api/session-insights/:course_date_id

Get real-time insights for active session

### WebSocket Events

#### Client -> Server

- `subscribe` - Subscribe to course_date_id updates
- `unsubscribe` - Unsubscribe from updates

#### Server -> Client

- `sentiment_alert` - Negative sentiment detected
- `confusion_detected` - Multiple students confused
- `engagement_update` - Participation metrics
- `topic_change` - New topic identified

## AI Analysis Features

### 1. Sentiment Analysis

- Positive/Negative/Neutral classification
- Confidence scores
- Emotional tone detection

### 2. Confusion Detection

- Keyword analysis (help, confused, stuck)
- Question pattern recognition
- Contextual understanding

### 3. Topic Clustering

- Automatic categorization of questions
- Trend identification
- Related concept grouping

### 4. Engagement Metrics

- Message frequency per student
- Response patterns
- Participation distribution

### 5. Suggested Actions

- When to pause for questions
- Topics needing review
- Students needing attention

## LLaMA Integration

### Option 1: Local LLaMA (Ollama)

Install Ollama: https://ollama.ai/

```bash
# Pull LLaMA model
ollama pull llama2

# Start Ollama server (runs on port 11434)
ollama serve
```

### Option 2: OpenAI API

Set `AI_PROVIDER=openai` and provide `OPENAI_API_KEY` in `.env`

### Prompts

Example prompt for sentiment analysis:

```
Analyze the sentiment and intent of this classroom chat message:
"{message_text}"

Provide:
1. Sentiment (positive/negative/neutral)
2. Confidence score (0-1)
3. Detected emotions
4. Whether student needs help (yes/no)
5. Urgency level (low/medium/high)

Response format: JSON
```

## Database Schema

### Chat Analysis Results (New Table)

```sql
CREATE TABLE chat_analysis (
    id SERIAL PRIMARY KEY,
    message_id INTEGER REFERENCES chat_logs(id),
    course_date_id INTEGER REFERENCES course_dates(id),
    student_id INTEGER REFERENCES users(id),
    sentiment VARCHAR(20),
    sentiment_score DECIMAL(3,2),
    indicators TEXT[],
    urgency VARCHAR(20),
    topics TEXT[],
    analyzed_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_chat_analysis_course_date ON chat_analysis(course_date_id);
CREATE INDEX idx_chat_analysis_timestamp ON chat_analysis(analyzed_at);
```

## Future Enhancements

- [ ] Multi-language support
- [ ] Custom alert rules per instructor
- [ ] Historical analysis and trends
- [ ] Student engagement profiles
- [ ] Automated response suggestions
- [ ] Integration with lesson content
- [ ] Video call transcript analysis
- [ ] Dashboard visualizations
- [ ] Export reports (PDF/CSV)
- [ ] Machine learning model fine-tuning

## Development Roadmap

### Phase 1: Basic Sentiment Analysis ✓

- Real-time message analysis
- Simple sentiment classification
- WebSocket integration

### Phase 2: Advanced Detection (Current)

- Confusion pattern recognition
- Topic clustering
- Engagement metrics

### Phase 3: Actionable Insights

- Instructor alerts
- Suggested responses
- Performance analytics

### Phase 4: ML Enhancement

- Fine-tuned models
- Personalized analysis
- Predictive insights

## Testing

```bash
# Test sentiment analysis
curl -X POST http://localhost:3003/api/analyze-message \
  -H "Content-Type: application/json" \
  -d '{
    "message_id": 1,
    "text": "I am really confused about arrays",
    "student_id": 123,
    "course_date_id": 456
  }'
```

## License

ISC

## Support

For issues or questions, contact the Frost development team.
