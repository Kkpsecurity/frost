const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');
const bodyParser = require('body-parser');
const generateSignature = require('../auth/generateSignature');

const app = express();

const PORT = process.env.ZOOM_NODE_PORT || 3017;

// Load environment variables from .env file
require('dotenv').config();

// Enable CORS for the specific frontend origin
app.use(cors({
    origin: 'https://frost-live.develc.cisadmin.com', // Replace with your frontend domain
    methods: ['GET', 'POST'], // Specify the allowed HTTP methods
    allowedHeaders: ['Content-Type', 'Authorization'], // Specify the allowed headers
    credentials: true, // If you need to expose cookies or other credentials
}));

// Add Helmet middleware for security
app.use(helmet());

// Apply rate limiting to all requests
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100, // limit each IP to 100 requests per windowMs
});
app.use(limiter);

// Parse incoming request bodies in a middleware before your handlers
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Basic route to check if the server is running
app.get('/', (req, res) => {
  res.send('Node.js server is running');
});

// Route to generate JWT for Zoom Meeting SDK
app.get('/generate-token', (req, res) => {
  const sdkKey = process.env.ZOOM_MEETING_SDK || 'your_sdk_key_here';
  const sdkSecret = process.env.ZOOM_MEETING_SECRET || 'your_sdk_secret_here';
  const meetingNumber = req.query.meetingNumber;
  let role = parseInt(req.query.role, 10);
  
  if (!sdkKey || !sdkSecret) {
    return res.status(500).json({ error: 'Zoom SDK credentials are missing in environment variables' });
  }

  if (!meetingNumber || isNaN(role)) {
    return res.status(400).json({ error: 'Missing or invalid meeting number or role' });
  }

  try {
    const signature = generateSignature(sdkKey, sdkSecret, meetingNumber, role); // Make sure generateSignature uses sdkKey and sdkSecret
    res.json({ signature });
  } catch (error) {
    console.error('Error generating signature:', error);
    res.status(500).json({ error: 'Failed to generate signature' });
  }
});


// Start the server
app.listen(PORT, '127.0.0.1', () => {
  console.log(`Server is running on http://localhost:${PORT}`);
});
