# Enhanced QR Code Generator - User Guide

## Overview

The Enhanced QR Code Generator is a professional tool for creating QR codes for meter reading operations. It features three main functions:

1. **Individual QR Generation** - Create QR codes for single meters
2. **Batch Generation** - Create multiple QR codes for active tenants
3. **QR Scanner Test** - Test QR code scanning functionality

## Getting Started

### Accessing the Enhanced QR Generator

1. Navigate to the QR Meter Reading system
2. Click on the QR Generator link or go directly to `qr-generator.html`
3. You'll see three tabs: Individual QR Codes, Batch Generation, and Test Scanner

## Individual QR Code Generation

### Creating a Single QR Code

1. **Select the "Individual QR Codes" tab** (active by default)
2. **Fill in the required information:**
   - **Property ID**: Enter the property identifier (e.g., PROP1)
   - **Unit Number**: Enter the unit number (e.g., UNIT001)
   - **Meter ID** (Optional): Enter meter identifier if available
   - **Property Name** (Optional): Enter property name for display

3. **Click "Generate QR Code"**
4. **The QR code will appear** with property information displayed professionally

### Download and Print Options

After generating a QR code, you can:

- **Download PNG**: Save the QR code as an image file
- **Print QR Code**: Print the QR code with property information
- **Copy Data**: Copy the QR code data to clipboard

### QR Code Information Display

Each generated QR code shows:
- Property name (or Property ID if name not provided)
- Unit number
- "Scan for Meter Reading" instruction
- Raw QR data for reference

## Batch QR Code Generation

### Overview

Batch generation allows you to create QR codes for multiple active tenants at once, saving significant time when deploying QR codes across properties.

### Steps for Batch Generation

1. **Select the "Batch Generation" tab**
2. **Wait for tenant data to load** (the system retrieves active tenants from the database)
3. **Use search and filter options:**
   - **Search box**: Type to search by tenant name, property, or unit
   - **Property filter**: Select a specific property to filter tenants

### Selecting Tenants

- **Individual selection**: Check the box next to each tenant you want
- **Select all visible**: Use the checkbox in the header to select all filtered tenants
- **Selection counter**: Shows how many tenants are currently selected

### Generating Batch QR Codes

1. **Select the tenants** you want QR codes for
2. **Click "Generate QR Codes"** (button shows number of selected tenants)
3. **Wait for generation to complete** (progress bar shows current status)
4. **Review the generated QR codes** in the results grid

### Batch Download Options

After generating batch QR codes:

- **Download PDF**: All QR codes in a print-ready PDF format
- **Download ZIP**: Individual QR code images in a ZIP file
- **Print All**: Print all QR codes in a professional grid layout

### Print Layout

QR codes are optimized for printing:
- **Standard size**: 60mm x 80mm per QR code
- **3-column grid**: Professional layout for standard paper
- **Property information**: Clearly displayed on each QR code
- **Print-ready**: High contrast for reliable scanning

## QR Scanner Test

### Testing QR Code Functionality

1. **Select the "Test Scanner" tab**
2. **Click "Start Camera Scanner"**
3. **Grant camera permissions** when prompted
4. **Hold a QR code** in front of the camera
5. **View the parsed results** showing:
   - Raw QR data
   - Property ID
   - Unit Number
   - Meter ID (if available)

### Scanner Instructions

For best results when testing:
- Ensure good lighting
- Hold the QR code steady
- Fill most of the camera view with the QR code
- Keep the code flat and unobstructed

### Troubleshooting Scanner Issues

If the scanner doesn't work:
- Check browser camera permissions
- Ensure you're using HTTPS (required for camera access)
- Try a different browser or device
- Make sure the QR code is clear and undamaged

## QR Code Data Format

### Understanding QR Code Content

The QR codes contain JSON data in this format:
```json
{
  "propertyId": "PROP1",
  "unitNumber": "UNIT001", 
  "meterId": null,
  "tenantCode": "T001",
  "propertyName": "Property Name"
}
```

### Compatibility

These QR codes are compatible with:
- The RMS Meter Reading mobile app
- Standard QR code readers (will show raw JSON data)
- The integrated QR Scanner Test functionality

## Tips for Effective Use

### Individual QR Generation

- **Use clear identifiers**: Make Property IDs and Unit Numbers easy to read
- **Add property names**: Makes identification easier for field staff
- **Test before printing**: Use the Scanner Test to verify QR codes work

### Batch Generation

- **Filter before selecting**: Use search and property filters to find specific tenants
- **Select by property**: Generate QR codes one property at a time for organization
- **Review before printing**: Check the generated QR codes before printing
- **Plan your deployment**: Consider how many QR codes you need to print at once

### Printing Guidelines

- **Use high-quality printers**: 300 DPI minimum for best scanning results
- **Print on white paper**: Provides best contrast for QR codes
- **Avoid scaling**: Print at actual size for optimal scanning
- **Test printed codes**: Scan a few printed QR codes to verify quality

## Troubleshooting

### Common Issues

**QR Code Won't Generate**
- Check that Property ID and Unit Number are entered
- Verify you have an internet connection
- Try refreshing the page

**Batch Generation Shows No Tenants**
- Check database connection
- Verify you have access permissions
- Contact system administrator if the issue persists

**Scanner Test Doesn't Work**
- Grant camera permissions in your browser
- Ensure you're using HTTPS
- Try a different browser or device

**Print Quality Issues**
- Use higher printer resolution (300 DPI or better)
- Ensure adequate ink/toner levels
- Print on white paper for best contrast

### Getting Help

If you encounter issues:
1. Check this user guide for solutions
2. Test with the QR Scanner Test function
3. Contact your system administrator
4. Report any consistent problems for technical support

## Best Practices

### For Field Deployment

- **Plan your QR placement**: Choose locations that are easily accessible but secure
- **Label clearly**: Include property and unit information near QR codes
- **Protect from weather**: Use appropriate materials for outdoor installations
- **Train staff**: Ensure field staff know how to scan and use QR codes

### For System Management

- **Regular updates**: Regenerate QR codes if tenant information changes
- **Backup codes**: Keep digital copies of all generated QR codes
- **Monitor usage**: Track which QR codes are being scanned regularly
- **Quality control**: Periodically test printed QR codes for readability

---

**Document Version**: 1.0  
**Last Updated**: January 2025  
**System**: RMS Enhanced QR Code Generator  
**Support**: Contact your RMS system administrator
