# Critical Issues Fix Plan - QR Meter Reading System

## Executive Summary
Three critical issues have been identified in the QR Meter Reading System that require immediate attention before production deployment. These issues affect the core functionality of reading persistence and billing integration.

## Critical Issues Identified

### Issue 1: Incorrect Previous Reading Calculation ⚠️ **CRITICAL**
**Problem**: The stored procedure `sp_t_SaveTenantReading` is not correctly retrieving the previous reading from the most recent reading for the unit.  
**Impact**: Previous readings are being saved incorrectly, affecting usage calculations and billing accuracy.  
**Root Cause**: The query for previous reading is not using the correct logic to get the last reading for the property+unit combination.

**Current Logic (INCORRECT)**:
```sql
-- Get last reading for the unit (property + unit combination)
SELECT TOP 1 
    @prevReading = current_reading
FROM t_tenant_reading r
INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
WHERE t.real_property_code = @propertyCode 
  AND t.unit_no = @unitNo
ORDER BY r.reading_date DESC;
```

**Required Fix**: Update the query to use the correct logic for retrieving the most recent reading for the unit, regardless of tenant changes.

### Issue 2: Missing Charge Code Integration ⚠️ **CRITICAL**
**Problem**: The system is not automatically creating entries in `t_tenant_reading_charges` for CUCF and CUCNF charge codes.  
**Impact**: Charge codes are not being linked to readings, breaking the billing workflow and preventing proper invoice generation.  
**Root Cause**: The stored procedure does not call `sp_t_TenantReading_Charges_Save` to create charge code entries.

**Required Fix**: Add calls to `sp_t_TenantReading_Charges_Save` for both CUCF and CUCNF charge codes after successfully saving the reading.

### Issue 3: Invoice Columns Not Set to NULL ⚠️ **CRITICAL**
**Problem**: Invoice-related columns in `t_tenant_reading_charges` should be left as NULL initially.  
**Impact**: May cause issues with billing workflow and invoice generation.  
**Root Cause**: Not explicitly setting invoice columns to NULL in the charge creation process.

**Required Fix**: Ensure `trc_invoice_no`, `trc_invoice_detail_id`, `trc_invoice_detail_reading_id` are explicitly set to NULL when creating charge entries.

## Detailed Fix Implementation Plan

### Phase 1: Fix Previous Reading Calculation

#### 1.1 Update Stored Procedure Query
**File**: `database/save-tenant-reading-procedure.sql`  
**Location**: Lines 95-105 (Previous Reading Retrieval Section)

**Current Code (INCORRECT)**:
```sql
-- Get last reading for the unit (property + unit combination)
SELECT TOP 1 
    @prevReading = current_reading
FROM t_tenant_reading r
INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
WHERE t.real_property_code = @propertyCode 
  AND t.unit_no = @unitNo
ORDER BY r.reading_date DESC;
```

**Fixed Code (CORRECT)**:
```sql
-- Get last reading for the unit (property + unit combination)
-- Use reading_date if available, otherwise fall back to date_created
SELECT TOP 1 
    @prevReading = current_reading
FROM t_tenant_reading r
INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
WHERE t.real_property_code = @propertyCode 
  AND t.unit_no = @unitNo
  AND r.reading_id != @readingId  -- Exclude current reading if it exists
ORDER BY ISNULL(r.reading_date, r.date_created) DESC;
```

#### 1.2 Add Validation Logic
Add validation to ensure previous reading is not null and is less than current reading:

```sql
-- Validate previous reading
IF @prevReading IS NOT NULL AND @prevReading > @currentReading
BEGIN
    RAISERROR('Current reading (%f) must be greater than or equal to previous reading (%f)', 16, 1, @currentReading, @prevReading);
    RETURN;
END
```

### Phase 2: Add Charge Code Integration

#### 2.1 Add Charge Code Creation Logic
**File**: `database/save-tenant-reading-procedure.sql`  
**Location**: After successful reading insertion (around line 180)

**Add After Reading Insertion**:
```sql
-- Create charge code entries for CUCF and CUCNF
DECLARE @chargeCode NVARCHAR(5);
DECLARE @chargeResult INT;

-- Create CUCF charge entry
SET @chargeCode = 'CUCF';
EXEC sp_t_TenantReading_Charges_Save 
    @strMode = 'SAVE',
    @reading_id = @readingId,
    @reading_charge_id = 0,
    @charge_code = @chargeCode,
    @uid = @createdBy,
    @company_code = (SELECT TOP 1 company_code FROM m_tenant WHERE tenant_code = @tenantCode),
    @ip_addr = @ipAddress;

-- Create CUCNF charge entry  
SET @chargeCode = 'CUCNF';
EXEC sp_t_TenantReading_Charges_Save 
    @strMode = 'SAVE',
    @reading_id = @readingId,
    @reading_charge_id = 0,
    @charge_code = @chargeCode,
    @uid = @createdBy,
    @company_code = (SELECT TOP 1 company_code FROM m_tenant WHERE tenant_code = @tenantCode),
    @ip_addr = @ipAddress;
```

#### 2.2 Update Charge Save Procedure
**File**: `database/RMS database stored procedure 2025-08-28 1600.sql`  
**Location**: `sp_t_TenantReading_Charges_Save` procedure (around line 14755)

**Ensure Invoice Columns are NULL**:
```sql
-- In the INSERT statement, explicitly set invoice columns to NULL
insert into t_tenant_reading_charges (trc_reading_id, trc_charge_code, trc_invoice_no, trc_invoice_detail_id, trc_invoice_detail_reading_id)	
select @reading_id, @charge_code, NULL, NULL, NULL
```

### Phase 3: Testing and Validation

#### 3.1 Test Previous Reading Calculation
- Test with existing readings for a unit
- Test with no previous readings (first reading)
- Test with tenant changes between readings
- Verify usage calculations are correct

#### 3.2 Test Charge Code Integration
- Verify CUCF and CUCNF entries are created in `t_tenant_reading_charges`
- Verify invoice columns are set to NULL
- Test with existing charge codes for tenant
- Test with no existing charge codes for tenant

#### 3.3 Test Complete Workflow
- End-to-end QR scan → save → verify database entries
- Check all tables are updated correctly
- Verify audit trail is maintained

## Implementation Steps

### Step 1: Update Stored Procedure
1. **Backup Current Procedure**: Create backup of current `sp_t_SaveTenantReading`
2. **Update Previous Reading Logic**: Fix the query to correctly retrieve previous reading
3. **Add Charge Code Integration**: Add calls to create CUCF and CUCNF entries
4. **Add Validation**: Add validation for reading values
5. **Test Procedure**: Test with sample data

### Step 2: Update Charge Save Procedure
1. **Review Current Logic**: Ensure `sp_t_TenantReading_Charges_Save` handles NULL invoice columns
2. **Update if Needed**: Modify to explicitly set invoice columns to NULL
3. **Test Integration**: Test charge code creation

### Step 3: Update API Response
1. **Update Response Format**: Ensure API returns correct previous reading value
2. **Add Charge Code Info**: Include charge code creation status in response
3. **Update Error Handling**: Handle charge code creation failures

### Step 4: Testing and Validation
1. **Unit Testing**: Test individual components
2. **Integration Testing**: Test complete workflow
3. **Data Validation**: Verify database entries are correct
4. **Performance Testing**: Ensure no performance degradation

## Risk Assessment

### High Risk
- **Data Integrity**: Incorrect previous reading calculations could affect billing accuracy
- **Billing Workflow**: Missing charge codes could break invoice generation
- **Production Impact**: Issues could affect live billing operations

### Medium Risk
- **Performance**: Additional database calls for charge code creation
- **Complexity**: More complex stored procedure logic

### Low Risk
- **Backward Compatibility**: Changes should not affect existing data
- **User Interface**: No UI changes required

## Success Criteria

### Functional Requirements
- [ ] Previous reading is correctly calculated from most recent reading for unit
- [ ] CUCF and CUCNF charge codes are automatically created for each reading
- [ ] Invoice columns in `t_tenant_reading_charges` are set to NULL initially
- [ ] Usage calculations are accurate (current - previous reading)
- [ ] All existing functionality continues to work

### Technical Requirements
- [ ] Stored procedure executes without errors
- [ ] Database transactions are properly handled
- [ ] Audit trail is maintained
- [ ] Performance is acceptable
- [ ] Error handling is comprehensive

### Business Requirements
- [ ] Billing workflow is not broken
- [ ] Invoice generation can proceed normally
- [ ] Data integrity is maintained
- [ ] System is ready for production deployment

## Timeline

### Immediate (Today)
- [ ] Update stored procedure with fixes
- [ ] Test previous reading calculation
- [ ] Test charge code integration

### Short Term (1-2 days)
- [ ] Complete testing and validation
- [ ] Update documentation
- [ ] Prepare for production deployment

### Medium Term (1 week)
- [ ] Deploy to production
- [ ] Monitor system performance
- [ ] User acceptance testing

## Conclusion

These critical issues must be addressed before the QR Meter Reading System can be deployed to production. The fixes are straightforward but require careful testing to ensure data integrity and system stability. The implementation plan provides a structured approach to resolving these issues while minimizing risk to the existing system.
