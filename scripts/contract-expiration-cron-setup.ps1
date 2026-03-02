# contract-expiration-cron-setup.ps1
#
# PowerShell script to configure Windows Task Scheduler for contract expiration alerts
#
# Usage: .\contract-expiration-cron-setup.ps1 -ProjectPath "C:\path\to\project" -PhpPath "C:\xampp\php\php.exe"
#

param(
    [Parameter(Mandatory=$true)]
    [string]$ProjectPath,
    
    [Parameter(Mandatory=$true)]
    [string]$PhpPath,
    
    [string]$LogPath = "C:\logs",
    [string]$TaskUser = $env:USERNAME
)

# Verify project path
if (-not (Test-Path "$ProjectPath\bin\console")) {
    Write-Error "Project path is invalid. bin/console not found in: $ProjectPath"
    Exit 1
}

# Verify PHP path
if (-not (Test-Path $PhpPath)) {
    Write-Error "PHP path is invalid: $PhpPath"
    Exit 1
}

# Create log directory
if (-not (Test-Path $LogPath)) {
    Write-Host "Creating log directory: $LogPath"
    New-Item -ItemType Directory -Path $LogPath | Out-Null
}

Write-Host ""
Write-Host "===================================="
Write-Host "Contract Expiration Task Scheduler Setup"
Write-Host "===================================="
Write-Host ""
Write-Host "Project Path: $ProjectPath"
Write-Host "PHP Path: $PhpPath"
Write-Host "Log Path: $LogPath"
Write-Host "Task User: $TaskUser"
Write-Host ""

# Define tasks
$tasks = @(
    @{
        Name = "ContractExpiration_Expired_Daily"
        Description = "Check for expired contracts and send SMS alerts"
        Time = "08:00"
        Command = "$PhpPath bin/console app:contract:expiration"
        LogFile = "$LogPath\contract-expiration.log"
    },
    @{
        Name = "ContractExpiration_Coming_Daily"
        Description = "Check for contracts expiring within 7 days"
        Time = "09:00"
        Command = "$PhpPath bin/console app:contract:expiration --days-ahead=7"
        LogFile = "$LogPath\contract-expiration-coming.log"
    }
)

# Requires admin rights
if (-not ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Error "This script requires administrator privileges!"
    Exit 1
}

# Create scheduled tasks
foreach ($task in $tasks) {
    Write-Host "Creating task: $($task.Name)"
    Write-Host "  Description: $($task.Description)"
    Write-Host "  Time: Daily at $($task.Time)"
    Write-Host "  Command: $($task.Command)"
    Write-Host "  LogFile: $($task.LogFile)"
    Write-Host ""
    
    # Create trigger
    $trigger = New-ScheduledTaskTrigger -Daily -At $task.Time
    
    # Create action
    $action = New-ScheduledTaskAction `
        -Execute $task.Command `
        -WorkingDirectory $ProjectPath `
        -Argument "`"> `"$($task.LogFile)`" 2>&1"
    
    # Create task settings
    $settings = New-ScheduledTaskSettingsSet `
        -MultipleInstances Parallel `
        -StartWhenAvailable `
        -RunOnlyIfNetworkAvailable `
        -RunOnlyIfIdle:$false
    
    # Register task
    try {
        # Remove existing task if it exists
        $existingTask = Get-ScheduledTask -TaskName $task.Name -ErrorAction SilentlyContinue
        if ($existingTask) {
            Write-Host "Removing existing task: $($task.Name)"
            $existingTask | Unregister-ScheduledTask -Confirm:$false
        }
        
        # Register new task
        Register-ScheduledTask `
            -TaskName $task.Name `
            -Trigger $trigger `
            -Action $action `
            -Settings $settings `
            -User $TaskUser `
            -Password (Read-Host "Enter password for $TaskUser" -AsSecureString | ConvertFrom-SecureString -AsPlainText) `
            -RunLevel Highest `
            -Description $task.Description | Out-Null
        
        Write-Host "✅ Task created successfully!`n"
    } catch {
        Write-Error "Failed to create task: $_"
    }
}

Write-Host "===================================="
Write-Host "✅ Task Scheduler setup completed!"
Write-Host "===================================="
Write-Host ""
Write-Host "Summary:"
Write-Host "  📅 Task 1: Expired contracts - Daily at 08:00"
Write-Host "  📅 Task 2: Expiring soon - Daily at 09:00"
Write-Host "  📁 Logs: $LogPath"
Write-Host ""
Write-Host "To verify tasks are installed:"
Write-Host "  Get-ScheduledTask | Where-Object {`$_.TaskName -like '*ContractExpiration*'}"
Write-Host ""
Write-Host "To view task details:"
Write-Host "  Get-ScheduledTask -TaskName ContractExpiration_Expired_Daily | Select-Object *"
Write-Host ""
Write-Host "To view logs:"
Write-Host "  Get-Content -Path '$LogPath\contract-expiration.log' -Tail 20 -Wait"
Write-Host ""
