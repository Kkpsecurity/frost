/** @format */

import axios from "axios";

const API_URL = "https://frost-live.develc.cisadmin.com/api/openai";

/**
 * Sends a request to the Laravel backend, which forwards it to OpenAI.
 * @param {Object} options - AI configuration options.
 * @param {string} options.prompt - The user's input.
 * @param {string} [options.model="gpt-4"] - The AI model to use.
 * @param {number} [options.temperature=0.7] - AI creativity level.
 * @param {string} [options.systemRole="AI Assistant"] - System instruction for AI behavior.
 * @returns {Promise<string>} - AI-generated response.
 */
export async function sendMessage({
  prompt,
  model = "gpt-4",
  temperature = 0.7,
  systemRole = "You are an AI assistant helping with security training for G and D Florida Weapons Licenses Class.",
}: {
  prompt: string;
  model?: string;
  temperature?: number;
  systemRole?: string;
}): Promise<string> {
  if (!prompt) throw new Error("Prompt is required.");

  try {
    const { data } = await axios.post(API_URL, {
      prompt,
      model,
      temperature,
      systemRole,
    });

    return data.choices[0]?.message?.content || "No response from AI.";
  } catch (error) {
    console.error("OpenAI Error:", error);
    return "Error connecting to AI.";
  }
}
