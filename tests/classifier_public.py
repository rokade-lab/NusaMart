"""Public plumbing examples; complete rules.py before adding decision assertions."""
import json,pathlib,subprocess,sys,unittest
ROOT=pathlib.Path(__file__).resolve().parents[1]
class Plumbing(unittest.TestCase):
 def command(self):
  return [sys.executable,'-m','nusamart_classifier','--inventory','docs/public-fixtures/inventory.csv','--sbom','docs/public-fixtures/sbom.json','--advisory','docs/advisory.simulation.json','--as-of','2026-11-07T11:00:00Z']
 def test_example_cli_has_structured_results_and_separate_summary(self):
  r=subprocess.run(self.command(),cwd=ROOT,text=True,capture_output=True)
  self.assertEqual(r.returncode,0,r.stderr)
  data=json.loads(r.stdout);self.assertTrue(data['simulation']);self.assertEqual(data['results'][0]['workload_id'],'checkout')
  self.assertIn(data['results'][0]['status'],['affected','not_affected','patched','mitigated','unknown'])
  for field in ('reason','confidence','evidence_refs','conflicts','underlying_assessment'):self.assertIn(field,data['results'][0])
  self.assertIn('exit 0 means processed',r.stderr)
 def test_second_advisory_runs_with_same_plumbing(self):
  cmd=self.command();cmd[cmd.index('--advisory')+1]='docs/advisory-npm.simulation.json'
  r=subprocess.run(cmd,cwd=ROOT,text=True,capture_output=True)
  self.assertEqual(r.returncode,0,r.stderr)
  self.assertEqual([x['workload_id'] for x in json.loads(r.stdout)['results']],['checkout','proxy'])
 def test_invalid_as_of_is_execution_error(self):
  cmd=self.command();cmd[-1]='not-a-time';r=subprocess.run(cmd,cwd=ROOT,text=True,capture_output=True)
  self.assertEqual(r.returncode,2);self.assertEqual(r.stdout,'');self.assertEqual(json.loads(r.stderr)['error'],'INVALID_INPUT')
 def test_missing_file_is_execution_error(self):
  cmd=self.command();cmd[4]='missing-file.csv';r=subprocess.run(cmd,cwd=ROOT,text=True,capture_output=True)
  self.assertEqual(r.returncode,2);self.assertEqual(r.stdout,'');self.assertEqual(json.loads(r.stderr)['error'],'INVALID_INPUT')
if __name__=='__main__':unittest.main()
