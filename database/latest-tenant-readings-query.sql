-- =============================================
-- Latest Tenant Readings per Property/Unit
-- Performance-Optimized Query
-- Author: SQL Performance Expert
-- Date: 2025-01-27
-- =============================================

-- OPTION 1: Using Window Functions (Recommended for SQL Server 2012+)
-- Most efficient approach using ROW_NUMBER() for getting latest records
WITH LatestReadings AS (
    SELECT 
        property_code,
        unit_no,
        property_name,
        tenant_code,
        tenant_name,
        terminated,
        current_reading,
        prev_reading,
        usage,
        reading_date,
        billing_from,
        billing_to,
        reading_date_from,
        reading_date_to,
        remarks,
        unit_desc,
        date_created,
        -- Rank readings by date (most recent first)
        -- Note: reading_date was added in schema updates, falls back to date_created or billing_to
        ROW_NUMBER() OVER (
            PARTITION BY property_code, unit_no 
            ORDER BY 
                ISNULL(reading_date, ISNULL(date_created, billing_to)) DESC
        ) as rn
    FROM vw_TenantReading
    WHERE 
        -- Filter out completely null readings (no current or previous reading)
        (current_reading IS NOT NULL OR prev_reading IS NOT NULL)
        -- Optional: Filter for active tenants only
        -- AND (terminated IS NULL OR terminated = 'N')
)
SELECT 
    property_code,
    unit_no,
    property_name,
    tenant_code,
    tenant_name,
    terminated,
    current_reading,
    prev_reading,
    usage,
    reading_date,
    billing_from,
    billing_to,
    reading_date_from,
    reading_date_to,
    remarks,
    unit_desc,
    date_created
FROM LatestReadings
WHERE rn = 1
ORDER BY property_code, unit_no;

-- =============================================
-- OPTION 2: Using Correlated Subquery (Alternative approach)
-- =============================================
/*
SELECT 
    v1.property_code,
    v1.unit_no,
    v1.property_name,
    v1.tenant_code,
    v1.tenant_name,
    v1.terminated,
    v1.current_reading,
    v1.prev_reading,
    v1.usage,
    v1.reading_date,
    v1.billing_from,
    v1.billing_to,
    v1.reading_date_from,
    v1.reading_date_to,
    v1.remarks,
    v1.unit_desc,
    v1.date_created
FROM vw_TenantReading v1
WHERE 
    (v1.current_reading IS NOT NULL OR v1.prev_reading IS NOT NULL)
    AND ISNULL(v1.reading_date, ISNULL(v1.date_created, v1.billing_to)) = (
        SELECT MAX(ISNULL(v2.reading_date, ISNULL(v2.date_created, v2.billing_to)))
        FROM vw_TenantReading v2
        WHERE v2.property_code = v1.property_code
          AND v2.unit_no = v1.unit_no
          AND (v2.current_reading IS NOT NULL OR v2.prev_reading IS NOT NULL)
    )
ORDER BY v1.property_code, v1.unit_no;
*/

-- =============================================
-- OPTION 3: Using EXISTS with Derived Table (For very large datasets)
-- =============================================
/*
SELECT 
    v.property_code,
    v.unit_no,
    v.property_name,
    v.tenant_code,
    v.tenant_name,
    v.terminated,
    v.current_reading,
    v.prev_reading,
    v.usage,
    v.reading_date,
    v.billing_from,
    v.billing_to,
    v.reading_date_from,
    v.reading_date_to,
    v.remarks,
    v.unit_desc,
    v.date_created
FROM vw_TenantReading v
INNER JOIN (
    SELECT 
        property_code,
        unit_no,
        MAX(ISNULL(reading_date, ISNULL(date_created, billing_to))) as max_reading_date
    FROM vw_TenantReading
    WHERE (current_reading IS NOT NULL OR prev_reading IS NOT NULL)
    GROUP BY property_code, unit_no
) latest ON v.property_code = latest.property_code
         AND v.unit_no = latest.unit_no
         AND ISNULL(v.reading_date, ISNULL(v.date_created, v.billing_to)) = latest.max_reading_date
WHERE (v.current_reading IS NOT NULL OR v.prev_reading IS NOT NULL)
ORDER BY v.property_code, v.unit_no;
*/

-- =============================================
-- PERFORMANCE OPTIMIZATION RECOMMENDATIONS
-- =============================================

-- 1. RECOMMENDED INDEXES for optimal performance:
-- Note: These are suggestions based on actual table structures
/*
-- Index on t_tenant_reading for date-based filtering and sorting
-- Uses actual columns: tenant_code, date_created, reading_date (added in schema updates)
CREATE NONCLUSTERED INDEX IX_t_tenant_reading_tenant_date 
ON t_tenant_reading (tenant_code, date_created DESC)
INCLUDE (current_reading, prev_reading, date_from, date_to, billing_date_from, billing_date_to, reading_date);

-- Index on m_tenant for property/unit lookups (actual columns)
CREATE NONCLUSTERED INDEX IX_m_tenant_property_unit 
ON m_tenant (real_property_code, building_code, unit_no)
INCLUDE (tenant_code, tenant_name, terminated);

-- Index on m_real_property for property name lookups (actual columns)
CREATE NONCLUSTERED INDEX IX_m_real_property_code 
ON m_real_property (real_property_code)
INCLUDE (real_property_name);
*/

-- 2. STATISTICS UPDATE (Run periodically):
/*
UPDATE STATISTICS t_tenant_reading;
UPDATE STATISTICS m_tenant;
UPDATE STATISTICS m_real_property;
*/

-- 3. QUERY HINTS (Use only if needed after testing):
-- Add OPTION (RECOMPILE) if parameter sniffing becomes an issue
-- Add OPTION (MAXDOP 4) for parallel execution on large datasets

-- =============================================
-- USAGE EXAMPLES
-- =============================================

-- Get latest readings for specific property:
/*
SELECT * FROM (
    -- [Insert Option 1 query here]
) latest_readings
WHERE property_code = 'PROP1'
ORDER BY unit_no;
*/

-- Get latest readings for active tenants only:
/*
SELECT * FROM (
    -- [Insert Option 1 query here]
) latest_readings
WHERE terminated IS NULL OR terminated = 'N'
ORDER BY property_code, unit_no;
*/

-- Get latest readings with usage > 0:
/*
SELECT * FROM (
    -- [Insert Option 1 query here]
) latest_readings
WHERE usage > 0
ORDER BY usage DESC;
*/
