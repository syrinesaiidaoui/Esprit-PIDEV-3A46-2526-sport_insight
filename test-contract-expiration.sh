#!/bin/bash
# Quick Test Script for Contract Expiration Alerts
# Usage: bash test-contract-expiration.sh

echo "╔════════════════════════════════════════════════════════╗"
echo "║   Contract Expiration Alerts - Quick Test Suite        ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_ROOT"

echo -e "${BLUE}📁 Project Root: $PROJECT_ROOT${NC}"
echo ""

# Test 1: Check Symfony command exists
echo -e "${YELLOW}[TEST 1]${NC} Checking if command is registered..."
if php bin/console list | grep -q "app:contract:expiration"; then
    echo -e "${GREEN}✅ PASS${NC} - Command is registered"
else
    echo -e "${RED}❌ FAIL${NC} - Command not found in list"
    exit 1
fi
echo ""

# Test 2: Check help works
echo -e "${YELLOW}[TEST 2]${NC} Testing command help..."
if php bin/console app:contract:expiration --help | grep -q "Check for expired contracts"; then
    echo -e "${GREEN}✅ PASS${NC} - Help works correctly"
else
    echo -e "${RED}❌ FAIL${NC} - Help output is missing"
    exit 1
fi
echo ""

# Test 3: Run in dry-run mode
echo -e "${YELLOW}[TEST 3]${NC} Running in dry-run mode (no SMS sent)..."
OUTPUT=$(php bin/console app:contract:expiration --dry-run 2>&1)

if echo "$OUTPUT" | grep -q "✅"; then
    echo -e "${GREEN}✅ PASS${NC} - Dry-run executed successfully"
    echo "$OUTPUT" | sed 's/^/    /'
else
    echo -e "${RED}❌ FAIL${NC} - Dry-run failed"
    echo "$OUTPUT" | sed 's/^/    /'
    exit 1
fi
echo ""

# Test 4: Test with 7-day lookahead
echo -e "${YELLOW}[TEST 4]${NC} Testing with 7-day lookahead..."
OUTPUT=$(php bin/console app:contract:expiration --days-ahead=7 --dry-run 2>&1)

if echo "$OUTPUT" | grep -q "✅"; then
    echo -e "${GREEN}✅ PASS${NC} - 7-day lookahead works"
    echo "$OUTPUT" | sed 's/^/    /'
else
    echo -e "${RED}❌ FAIL${NC} - 7-day lookahead failed"
    echo "$OUTPUT" | sed 's/^/    /'
    exit 1
fi
echo ""

# Test 5: Verbose mode
echo -e "${YELLOW}[TEST 5]${NC} Testing verbose mode..."
OUTPUT=$(php bin/console app:contract:expiration --dry-run -v 2>&1)

if echo "$OUTPUT" | grep -q "✅\|Summary"; then
    echo -e "${GREEN}✅ PASS${NC} - Verbose mode works"
else
    echo -e "${RED}❌ FAIL${NC} - Verbose output missing"
    exit 1
fi
echo ""

# Test 6: Check repository methods exist
echo -e "${YELLOW}[TEST 6]${NC} Checking repository methods..."
if grep -q "findByExpirationDate\|findExpiringWithinDays" src/Repository/ContratSponsorRepository.php; then
    echo -e "${GREEN}✅ PASS${NC} - Repository methods defined"
else
    echo -e "${RED}❌ FAIL${NC} - Repository methods not found"
    exit 1
fi
echo ""

# Test 7: Documentation files
echo -e "${YELLOW}[TEST 7]${NC} Checking documentation..."
DOCS_FOUND=0
[ -f "CONTRACT_EXPIRATION_SETUP.md" ] && echo -e "${GREEN}✅${NC} CONTRACT_EXPIRATION_SETUP.md" && ((DOCS_FOUND++))
[ -f "CRON_SETUP.md" ] && echo -e "${GREEN}✅${NC} CRON_SETUP.md" && ((DOCS_FOUND++))
[ -f "IMPLEMENTATION_SUMMARY.md" ] && echo -e "${GREEN}✅${NC} IMPLEMENTATION_SUMMARY.md" && ((DOCS_FOUND++))

if [ $DOCS_FOUND -eq 3 ]; then
    echo -e "${GREEN}✅ PASS${NC} - All documentation present"
else
    echo -e "${YELLOW}⚠️  WARNING${NC} - Some documentation missing ($DOCS_FOUND/3)"
fi
echo ""

# Test 8: Setup scripts
echo -e "${YELLOW}[TEST 8]${NC} Checking setup scripts..."
SCRIPTS_FOUND=0
[ -f "scripts/contract-expiration-cron-setup.sh" ] && echo -e "${GREEN}✅${NC} Linux/Mac setup script" && ((SCRIPTS_FOUND++))
[ -f "scripts/contract-expiration-cron-setup.ps1" ] && echo -e "${GREEN}✅${NC} Windows setup script" && ((SCRIPTS_FOUND++))

if [ $SCRIPTS_FOUND -eq 2 ]; then
    echo -e "${GREEN}✅ PASS${NC} - Both setup scripts present"
else
    echo -e "${YELLOW}⚠️  WARNING${NC} - Some setup scripts missing ($SCRIPTS_FOUND/2)"
fi
echo ""

# Summary
echo "╔════════════════════════════════════════════════════════╗"
echo "║                    TEST SUMMARY                        ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""
echo -e "${GREEN}✅ All core tests passed!${NC}"
echo ""
echo "Next steps:"
echo "  1. Create test contracts with expiring dates"
echo "  2. Run: php bin/console app:contract:expiration --dry-run -v"
echo "  3. Setup cron using: bash scripts/contract-expiration-cron-setup.sh $(pwd)"
echo ""

exit 0
