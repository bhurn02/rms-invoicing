# RMS Invoicing System

Realty Management Services (RMS) is a property management platform tailored for handling invoicing, tenant tracking, billing, and utility charge management. It supports a wide range of operations for residential, commercial, and warehouse properties under TanHoldings.

![RMS Screenshot](./assets/screenshots/rms-dashboard.png)

## 🔧 Features Overview

### 🏠 Core System Features

- **Real Property Management**  
  Manage units, tenant assignments, and property configurations.
  
- **Billing Module**  
  Generate tenant readings, calculate charges, and generate invoices per billing cycle.

- **Collection & Payments**  
  Security deposit tracking and payment or SAP uploading integration.

- **Utility Modules**  
  - **Invoice Alerts:** Send email notifications for pending invoices.  
  - **Unpost/Unvoid Invoices** for corrections.  
  - **Upload Aging Reports** to analyze tenant balances.  
  - **Mass Update Tools** for charge amounts and tenant settings.

### 📨 Invoice Dispatcher

The Invoice Dispatcher is a batch email system within the Realty Management Services (RMS) that sends invoices to tenants with advanced features:

#### ✨ Highlights

- **Responsive Interface** with toggles, animations, and dark mode support.
- **Batch Sending**  
  Emails are sent in batches (default: 5 at a time) to avoid timeouts and improve control.
- **Progress Tracker**  
  Real-time progress bar and batch summary logs.
- **Test Mode Toggle**  
  Option to simulate email sending for testing purposes.
- **Sanitized Email Detection**  
  Detects malformed email inputs (quotes, extra delimiters, misplaced CCs) and auto-corrects them.
- **Detailed Logging**  
  - Skipped invoices with reason  
  - Successful sends  
  - Partially sanitized emails with original and sanitized output  
- **Export Logs**  
  Download batch results in `.csv` or `.txt` format for documentation.
- **Search Filter**  
  Quickly locate specific invoices or tenants.
- **Auto Scrollable Tables**  
  Prevents long page scrolls by fixing table heights relative to screen size.

---

## 📂 Project Structure

