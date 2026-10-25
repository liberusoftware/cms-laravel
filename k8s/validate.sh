#!/usr/bin/env bash
# Validate Kubernetes manifests for Liberu CMS Laravel

set -e

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

info()    { echo -e "${GREEN}[INFO]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC} $1"; }

ERRORS=0

check_command() {
    if ! command -v "$1" >/dev/null 2>&1; then
        warn "$1 not found — skipping related checks"
        return 1
    fi
    return 0
}

validate_yaml() {
    local file="$1"
    if check_command python3; then
        if python3 -c "import yaml; list(yaml.safe_load_all(open('$file')))" 2>/dev/null; then
            success "YAML syntax valid: $file"
        else
            error "YAML syntax error: $file"
            ERRORS=$((ERRORS + 1))
        fi
    fi
}

info "=== Liberu CMS Kubernetes Manifest Validation ==="

# Check required files exist
REQUIRED_FILES=(
    "k8s/base/kustomization.yaml"
    "k8s/base/namespace.yaml"
    "k8s/base/configmap.yaml"
    "k8s/base/secret.yaml"
    "k8s/base/deployment.yaml"
    "k8s/base/queue-worker.yaml"
    "k8s/base/service.yaml"
    "k8s/base/ingress.yaml"
    "k8s/base/mysql-statefulset.yaml"
    "k8s/base/mysql-service.yaml"
    "k8s/base/pvc.yaml"
    "k8s/base/redis.yaml"
    "k8s/base/network-policy.yaml"
    "k8s/base/resource-quota.yaml"
    "k8s/overlays/production/kustomization.yaml"
    "k8s/overlays/development/kustomization.yaml"
)

info "Checking required manifest files..."
for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        success "Found: $file"
    else
        error "Missing: $file"
        ERRORS=$((ERRORS + 1))
    fi
done

# Validate YAML syntax
info "Validating YAML syntax..."
for file in k8s/base/*.yaml k8s/overlays/production/*.yaml k8s/overlays/development/*.yaml; do
    [ -f "$file" ] && validate_yaml "$file"
done

# Validate with kubectl if available
if check_command kubectl; then
    info "Validating with kubectl (dry-run)..."

    for overlay in production development; do
        if kubectl kustomize "k8s/overlays/$overlay" >/dev/null 2>&1; then
            success "Kustomize overlay valid: $overlay"
        else
            error "Kustomize overlay invalid: $overlay"
            ERRORS=$((ERRORS + 1))
        fi
    done
fi

echo ""
if [ "$ERRORS" -eq 0 ]; then
    echo -e "${GREEN}=== All validations passed ===${NC}"
    exit 0
else
    echo -e "${RED}=== Validation failed with $ERRORS error(s) ===${NC}"
    exit 1
fi
