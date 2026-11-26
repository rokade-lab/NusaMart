"""Offline stdlib-only CLI: JSON stdout, human summary stderr; no network code."""
import argparse,csv,datetime as dt,json,sys
from .rules import assess
from .schema import validate

def timestamp(value):
    parsed=dt.datetime.fromisoformat(value.replace('Z','+00:00'))
    if parsed.tzinfo is None:raise ValueError('timezone required')
    return parsed

def main():
    p=argparse.ArgumentParser(description='Offline NusaMart SIMULATION triage; exit0 means processed, not safe.')
    for arg in ('inventory','sbom','advisory'):p.add_argument('--'+arg,required=True)
    p.add_argument('--mitigations');p.add_argument('--as-of')
    a=p.parse_args()
    try:
        with open(a.inventory,newline='') as f:
            reader=csv.DictReader(f)
            if not reader.fieldnames or len(reader.fieldnames)!=len(set(reader.fieldnames)):raise ValueError('Duplicate CSV header')
            inventory=list(reader)
        with open(a.sbom) as f:sbom=json.load(f)
        with open(a.advisory) as f:advisory=json.load(f)
        mitigations=[]
        if a.mitigations:
            with open(a.mitigations) as f:mitigations=json.load(f)['mitigations']
        as_of=timestamp(a.as_of) if a.as_of else dt.datetime.now(dt.timezone.utc)
        validate(inventory,sbom,advisory,mitigations)
        results=assess(inventory,sbom,advisory,mitigations,as_of)
        print(json.dumps({'scenario_version':advisory['scenario_version'],'simulation':True,'as_of':as_of.isoformat(),'results':results},sort_keys=True))
        counts={s:sum(r['status']==s for r in results) for s in ('affected','not_affected','patched','mitigated','unknown')}
        print('SIMULATION triage: '+', '.join(f'{n} {s}' for s,n in counts.items())+'; exit 0 means processed.',file=sys.stderr)
        return 0
    except (OSError,ValueError,KeyError,TypeError,AttributeError,csv.Error):
        print(json.dumps({'error':'INVALID_INPUT','message':'Cannot process input schema/files/timestamps; no classification produced.'}),file=sys.stderr)
        return 2
if __name__=='__main__':sys.exit(main())
