# Google Gemini AI Integration Guide (Free Tier) - NeoERP

This guide explains how to configure and utilize the **Google Gemini Free Tier AI** integration in NeoERP for intelligent workflow automation, automated lead scoring, smart customer communications, quotation scope generation, and real-time copilot assistance.

---

## 1. Getting a Free Gemini API Key
Google AI Studio offers a **100% Free Tier** with generous limits:
- **Rate Limit:** 15 Requests Per Minute (RPM)
- **Daily Quota:** 1,500 Requests Per Day (RPD)
- **Token Quota:** 1,000,000 Tokens Per Minute (TPM)
- **Recommended Model:** `gemini-1.5-flash` (Fast, lightweight, structured JSON support) or `gemini-2.0-flash`

### Steps to obtain your key:
1. Visit [Google AI Studio](https://aistudio.google.com/app/apikey).
2. Sign in with any Google account.
3. Click **"Create API key"** (or select an existing project).
4. Copy your API Key (e.g., `AIzaSy...`).

---

## 2. Configuration

### Option A: Via the Web Interface (Recommended)
1. In the NeoERP sidebar, click **Configuration -> Gemini AI Hub** (or the **Gemini AI Studio** quick link).
2. Click the **API Settings** button in the header.
3. Paste your Gemini API key and select your preferred model (`gemini-1.5-flash`).
4. Click **Save Settings** and test connectivity using the **"Test Connection"** button.

### Option B: Via `.env` File
Add the following lines to your project's `.env` file:
```env
# Google Gemini AI Integration (Free Tier)
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-1.5-flash
```

---

## 3. Implemented Workflows & Features

### 🎯 1. AI Lead & Enquiry Qualification & Scoring
- **Location:** Enquiries Details (`/enquiries/{id}`) and Leads Details (`/leads/{id}`).
- **How it works:**
  - Click the **"✨ AI Analyze"** button in the header of any enquiry or lead.
  - Gemini analyzes customer requirements, urgency signals, and scope clarity.
  - Generates:
    - **Qualification Score (1-100)** with color-coded badges
    - **Urgency Assessment** (Low / Medium / High / Urgent)
    - **Executive Summary**
    - **Key Requirements list**
    - **Actionable Next Steps** for estimators and managers.

### ✉️ 2. Smart Email & Follow-Up Reply Drafter
- **Location:** Lead / Enquiry Header and Gemini AI Hub.
- **How it works:**
  - Click **"✨ AI Reply"** in any Lead/Enquiry page.
  - Choose the purpose (e.g. *Acknowledge & Propose Site Visit*, *Quotation Cover Letter*, *Follow-up*).
  - Gemini drafts an executive-ready, customized email response with 1-click clipboard copy.

### 📋 3. Quotation Scope of Work & Deliverables Generator
- **Location:** Gemini AI Hub (`/gemini` -> *Quotation Scope Generator* tab).
- **How it works:**
  - Enter project title, client name, and work description.
  - Gemini outputs structured, itemized deliverables, material estimates, inclusions, exclusions, and warranty clauses formatted for proposal documents.

### 🛡️ 4. Health & Safety RAMS Checklist Suggester
- **Location:** Gemini AI Hub (`/gemini` -> *RAMS Safety Suggester* tab).
- **How it works:**
  - Input the job trade and description (e.g., *Roofing Solar Panel Installation & Wiring*).
  - Gemini automatically produces hazard matrices, risk levels, control mitigations, required PPE lists, and pre-job safety checklists.

### 🤖 5. Global NeoERP AI Assistant (Copilot)
- **Location:** Top navigation bar (sparkles icon `✨`) across the entire ERP.
- **How it works:**
  - Click the AI icon in the navbar from any page.
  - Chat in real-time with the AI copilot for operational suggestions, task prioritization, email drafting, or compliance advice.

---

## 4. Architecture & Key Files

| File | Purpose |
|------|---------|
| [`config/gemini.php`](file:///c:/Users/dev/Desktop/neoerp-main/config/gemini.php) | Configuration settings for model, API key, and generation parameters. |
| [`app/Services/GeminiService.php`](file:///c:/Users/dev/Desktop/neoerp-main/app/Services/GeminiService.php) | Core API service communicating with Google Generative AI REST endpoints. |
| [`app/Http/Controllers/GeminiAIController.php`](file:///c:/Users/dev/Desktop/neoerp-main/app/Http/Controllers/GeminiAIController.php) | Controller handling Gemini AI routes, AJAX endpoints, and workflow logic. |
| [`resources/views/gemini/index.blade.php`](file:///c:/Users/dev/Desktop/neoerp-main/resources/views/gemini/index.blade.php) | Gemini AI Hub & interactive testing playground. |
| [`resources/views/partials/gemini_assistant_modal.blade.php`](file:///c:/Users/dev/Desktop/neoerp-main/resources/views/partials/gemini_assistant_modal.blade.php) | Global conversational ERP copilot modal. |
| [`resources/views/partials/gemini_workflow_modals.blade.php`](file:///c:/Users/dev/Desktop/neoerp-main/resources/views/partials/gemini_workflow_modals.blade.php) | Reusable popup modals for Lead AI Analysis & Smart Email Drafting. |
| [`routes/web.php`](file:///c:/Users/dev/Desktop/neoerp-main/routes/web.php) | Web routes under `auth` middleware for Gemini features. |
