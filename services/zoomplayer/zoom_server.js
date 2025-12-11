const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');
const bodyParser = require('body-parser');
const generateServerSignature = require("../auth/generateServerSignature");

const app = express();

// Load environment variables from root .env file
const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '../../.env') });

const PORT = process.env.ZOOM_NODE_PORT || 3017;

// Enable CORS for multiple frontend origins (local and production) - BEFORE helmet
app.use(
    cors({
        origin: function (origin, callback) {
            // Allow requests with no origin (like mobile apps or curl requests)
            if (!origin) return callback(null, true);

            const allowedOrigins = [
                "https://frost-live.develc.cisadmin.com",
                "http://frost.test",
                "https://frost.test",
                "http://localhost",
                "https://localhost",
            ];

            if (allowedOrigins.indexOf(origin) !== -1) {
                callback(null, true);
            } else {
                console.warn("CORS blocked origin:", origin);
                callback(null, false);
            }
        },
        methods: ["GET", "POST", "OPTIONS"],
        allowedHeaders: ["Content-Type", "Authorization"],
        credentials: true,
        optionsSuccessStatus: 200,
    })
);

// Add Helmet middleware for security - AFTER CORS
app.use(
    helmet({
        crossOriginResourcePolicy: { policy: "cross-origin" },
        crossOriginOpenerPolicy: { policy: "same-origin-allow-popups" },
    })
);

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

// Route to generate JWT for Zoom Meeting SDK (GET - legacy)
app.get("/generate-token", (req, res) => {
    const sdkKey = process.env.ZOOM_MEETING_SDK || "your_sdk_key_here";
    const sdkSecret = process.env.ZOOM_MEETING_SECRET || "your_sdk_secret_here";
    const meetingNumber = req.query.meetingNumber;
    let role = parseInt(req.query.role, 10);

    if (!sdkKey || !sdkSecret) {
        return res.status(500).json({
            error: "Zoom SDK credentials are missing in environment variables",
        });
    }

    if (!meetingNumber || isNaN(role)) {
        return res
            .status(400)
            .json({ error: "Missing or invalid meeting number or role" });
    }

    try {
        const signature = generateSignature(
            sdkKey,
            sdkSecret,
            meetingNumber,
            role
        );
        res.json({ signature });
    } catch (error) {
        console.error("Error generating signature:", error);
        res.status(500).json({ error: "Failed to generate signature" });
    }
});

// Route to generate signature for Zoom Meeting SDK (POST - new)
app.post("/generate-signature", (req, res) => {
    const sdkKey = process.env.ZOOM_MEETING_SDK || "your_sdk_key_here";
    const sdkSecret = process.env.ZOOM_MEETING_SECRET || "your_sdk_secret_here";
    const { meetingNumber, role } = req.body;

    console.log("Signature request received:", { meetingNumber, role });

    if (!sdkKey || !sdkSecret) {
        console.error("Zoom SDK credentials missing");
        return res.status(500).json({
            success: false,
            message:
                "Zoom SDK credentials are missing in environment variables",
        });
    }

    if (!meetingNumber || role === undefined) {
        console.error("Invalid request:", { meetingNumber, role });
        return res.status(400).json({
            success: false,
            message: "Missing or invalid meeting number or role",
        });
    }

    try {
        const signature = generateServerSignature(
            sdkKey,
            sdkSecret,
            meetingNumber,
            parseInt(role, 10)
        );
        console.log("Signature generated successfully");
        res.json({
            success: true,
            signature: signature,
            sdkKey: sdkKey,
            meetingNumber: meetingNumber,
        });
    } catch (error) {
        console.error("Error generating signature:", error);
        res.status(500).json({
            success: false,
            message: "Failed to generate signature",
            error: error.message,
        });
    }
});

// Global error handlers to prevent crashes
process.on("uncaughtException", (error) => {
    console.error("Uncaught Exception:", error);
});

process.on("unhandledRejection", (reason, promise) => {
    console.error("Unhandled Rejection at:", promise, "reason:", reason);
});

// Start the server
const server = app.listen(PORT, "127.0.0.1", () => {
    console.log(`Server is running on http://localhost:${PORT}`);
    console.log("Allowed origins:", [
        "http://frost.test",
        "https://frost.test",
        "http://localhost",
    ]);
});

server.on("error", (error) => {
    console.error("Server error:", error);
    process.exit(1);
});
