"""Participant exercise: complete assess() using the simulation contract.

Never infer safe status from a port, a tag, or missing version. Keep reasons,
confidence, evidence refs, conflicts, and underlying affected assessment visible.
"""
def assess(inventory,sbom,advisory,mitigations,as_of):
    # Canonical component names: php, nginx, nginx-proxy-manager. Unknown names/aliases remain unknown.
    # Matrix rows: with 'series' (one minor series) or without 'series' (cross-series range).
    # TODO: reconcile identities/versions/prerequisites, apply simulated matrix,
    # and then verified nonexpired mitigation. The plumbing is ready to run.
    return [{'workload_id':workload,'status':'unknown','reason':'participant rules not implemented','confidence':'low','evidence_refs':sorted({r['source_ref'] for r in inventory if r['workload_id']==workload}),'conflicts':[],'underlying_assessment':None} for workload in sorted({r['workload_id'] for r in inventory})]
