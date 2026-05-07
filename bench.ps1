Add-Type -AssemblyName System.Net.Http
='http://127.0.0.1:8000/equipement/'
=10; =120
 = New-Object System.Net.Http.HttpClientHandler
.AutomaticDecompression = [System.Net.DecompressionMethods]::GZip -bor [System.Net.DecompressionMethods]::Deflate
 = [System.Net.Http.HttpClient]::new()
.Timeout = [TimeSpan]::FromSeconds(15)

function Invoke-Bench([string]){
    =[System.Diagnostics.Stopwatch]::StartNew()
     = New-Object System.Collections.Generic.List[double]
     = New-Object System.Collections.Generic.List[int]
    for(=0;  -lt ; +=){
        =@()
        for(=0;  -lt  -and (+) -lt ; ++){
             += [System.Threading.Tasks.Task]::Run({ param(,)
                =[System.Diagnostics.Stopwatch]::StartNew()
                try {
                     = .GetAsync().GetAwaiter().GetResult()
                    .Stop()
                    return @{ ms = .Elapsed.TotalMilliseconds; code = [int].StatusCode }
                } catch {
                    .Stop(); return @{ ms = .Elapsed.TotalMilliseconds; code = 0 }
                }
            }, , )
        }
        foreach( in ){
             = .GetAwaiter().GetResult()
            .Add([double].ms)
            .Add([int].code)
        }
    }
    .Stop()
     = .ToArray(); [Array]::Sort()
    function GetPct(,[double]){ if(.Length -eq 0){ return 0 };  = [Math]::Min([Math]::Max([int][Math]::Round((/100)*.Length)-1,0), .Length-1); return [Math]::Round([],2) }
     = [Math]::Round(( | Measure-Object -Average).Average,2)
    [PSCustomObject]@{
        label=
        requests=.Count
        success=( | Where-Object { -ge 200 -and  -lt 400}).Count
        fail=( | Where-Object { -lt 200 -or  -ge 400}).Count
        rps=[Math]::Round(.Count / .Elapsed.TotalSeconds,2)
        avg_ms=
        p50_ms=GetPct  50
        p95_ms=GetPct  95
        p99_ms=GetPct  99
        max_ms=[Math]::Round([-1],2)
    }
}
 = Invoke-Bench 'cold'
 = Invoke-Bench 'warm'


