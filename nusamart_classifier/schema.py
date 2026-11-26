"""Shared input contract validation. Data uncertainty belongs in rule results."""
import re
# Observation vocabulary is case-sensitive; unknown names stay uncertain in rules.
KNOWN_COMPONENTS=('php','nginx','nginx-proxy-manager')
# Advisory prerequisite vocabulary: observed executable/app of the advisory component.
KNOWN_PREREQUISITES=('php_enabled','npm_enabled')
INVENTORY_FIELDS=('workload_id','owner','exposure','criticality','blackout','image_id','component','runtime_version','prerequisite','observed_at','source_ref','scenario_version')
BOM_FIELDS=('workload_id','image_id','component','version','prerequisite','observed_at','source_ref','scenario_version')

def release(value):
    if not isinstance(value,str) or not re.fullmatch(r'(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)',value):raise ValueError('Invalid matrix version')
    return tuple(map(int,value.split('.')))

def validate(inventory,sbom,advisory,mitigations):
    if not isinstance(inventory,list) or not inventory:raise ValueError('Empty inventory')
    for row in inventory:
        if not isinstance(row,dict) or not set(INVENTORY_FIELDS)<=row.keys() or None in row or not isinstance(row['workload_id'],str) or not row['workload_id'].strip():raise ValueError('Invalid inventory schema')
        if any(row[k] is None for k in INVENTORY_FIELDS):raise ValueError('Truncated CSV row')
    if not isinstance(sbom,dict) or sbom.get('format')!='nusamart-runtime-sbom-1' or not isinstance(sbom.get('scenario_version'),str) or not isinstance(sbom.get('components'),list):raise ValueError('Invalid SBOM')
    for row in sbom['components']:
        if not isinstance(row,dict) or not set(BOM_FIELDS)<=row.keys() or not isinstance(row['workload_id'],str) or not row['workload_id']:raise ValueError('Invalid SBOM component')
    if not isinstance(advisory,dict) or advisory.get('simulation') is not True or not all(isinstance(advisory.get(k),str) and advisory[k].strip() for k in ('advisory_id','scenario_version','component')) or advisory.get('prerequisite') not in KNOWN_PREREQUISITES:raise ValueError('Invalid simulated advisory')
    matrix=advisory.get('matrix')
    if not isinstance(matrix,list) or not matrix:raise ValueError('Empty matrix')
    # Row forms: {'series','affected_from','fixed'} bounded to one minor series, or
    # {'affected_from','fixed'} without series for a range that crosses minor series.
    series=set()
    for row in matrix:
        if not isinstance(row,dict) or not {'affected_from','fixed'}<=row.keys():raise ValueError('Invalid matrix')
        start,fixed=release(row['affected_from']),release(row['fixed'])
        if start>=fixed:raise ValueError('Inconsistent matrix')
        if 'series' in row:
            if row['series'] in series or row['series']!='.'.join(map(str,start[:2])) or row['series']!='.'.join(map(str,fixed[:2])):raise ValueError('Inconsistent matrix')
            series.add(row['series'])
    if not isinstance(mitigations,list) or any(not isinstance(m,dict) for m in mitigations):raise ValueError('Invalid mitigations')
