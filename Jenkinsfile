
pipeline {
    agent any

    options {
        timestamps()
        disableConcurrentBuilds()
        skipDefaultCheckout(true)
    }

    environment {
        DOCKER_EXE = 'C:\\Users\\ivanu\\AppData\\Local\\Programs\\DockerDesktop\\resources\\bin\\docker.exe'
        DOCKERHUB_REPO = 'ivanlara30'
        IMAGE_TAG = "jenkins-${BUILD_NUMBER}"
        DOCKER_CONFIG = 'C:\\ProgramData\\Jenkins\\.docker'
    }

    stages {
        stage('Diagnosticar flujo completo Docker Hub') {
    steps {
        withCredentials([usernamePassword(
            credentialsId: 'dockerhub-credentials',
            usernameVariable: 'DH_USER',
            passwordVariable: 'DH_PAT'
        )]) {
            powershell '''
                $pair = "$($env:DH_USER):$($env:DH_PAT)"
                $bytes = [System.Text.Encoding]::UTF8.GetBytes($pair)
                $basic = [Convert]::ToBase64String($bytes)

                try {
                    $auth = Invoke-RestMethod `
                        -Uri "https://auth.docker.io/token?service=registry.docker.io&scope=repository:ivanlara30/bugistore-auth-service:pull,push" `
                        -Headers @{ Authorization = "Basic $basic" }

                    if (-not $auth.token) {
                        throw "Docker Hub no devolvio un Bearer token."
                    }

                    Write-Host "PASO 1: Bearer token obtenido correctamente."

                    $registry = Invoke-WebRequest `
                        -Uri "https://registry-1.docker.io/v2/" `
                        -Headers @{ Authorization = "Bearer $($auth.token)" } `
                        -UseBasicParsing

                    Write-Host "PASO 2: HTTP $([int]$registry.StatusCode)"

                    if ([int]$registry.StatusCode -eq 200) {
                        Write-Host "RESULTADO: Flujo Bearer completado correctamente."
                    } else {
                        throw "El registro no acepto el Bearer token."
                    }
                }
                catch {
                    $status = $_.Exception.Response.StatusCode.value__
                    Write-Host "ERROR HTTP: $status"
                    Write-Host "DETALLE: $($_.Exception.Message)"
                    exit 1
                }
                finally {
                    $pair = $null
                    $basic = $null
                    $auth = $null
                    [Array]::Clear($bytes, 0, $bytes.Length)
                }
            '''
        }
    }
}
        stage('Checkout') {
            steps {
                echo 'Descargando el repositorio BugiStore...'
                checkout scm
            }
        }

        stage('Pruebas de sintaxis PHP') {
            steps {
                powershell '''
                    $ErrorActionPreference = "Stop"

                    & $env:DOCKER_EXE pull php:8.2-cli
                    if ($LASTEXITCODE -ne 0) {
                        throw "No se pudo descargar PHP 8.2"
                    }

                    $files = Get-ChildItem -Path . -Recurse -Filter *.php -File |
                        Where-Object {
                            $_.FullName -notmatch '\\\\.git\\\\|\\\\backup-encoding-'
                        }

                    if ($files.Count -eq 0) {
                        throw "No se encontraron archivos PHP"
                    }

                    foreach ($file in $files) {
                        $relative = $file.FullName.Substring($env:WORKSPACE.Length).TrimStart('\\')
                        $relative = $relative.Replace('\\', '/')

                        Write-Host "Validando: $relative"

                        & $env:DOCKER_EXE run --rm `
                            -v "${env:WORKSPACE}:/workspace" `
                            -w /workspace `
                            php:8.2-cli php -l "/workspace/$relative"

                        if ($LASTEXITCODE -ne 0) {
                            throw "Error de sintaxis en: $relative"
                        }
                    }

                    Write-Host "Todas las pruebas de sintaxis PHP finalizaron correctamente."
                '''
            }
        }

        stage('Construir imagenes Docker') {
            steps {
                powershell '''
                    $ErrorActionPreference = "Stop"

                    $services = @(
                        "auth-service",
                        "user-service",
                        "game-service",
                        "purchase-service",
                        "api-gateway"
                    )

                    foreach ($service in $services) {
                        $image = "$env:DOCKERHUB_REPO/bugistore-$service`:$env:IMAGE_TAG"

                        Write-Host "Construyendo $image"

                        & $env:DOCKER_EXE build `
                            -f docker/php-service.Dockerfile `
                            --build-arg "SERVICE_DIR=$service" `
                            -t $image .

                        if ($LASTEXITCODE -ne 0) {
                            throw "Fallo la construccion de $service"
                        }
                    }

                    $frontendImage = "$env:DOCKERHUB_REPO/bugistore-frontend`:$env:IMAGE_TAG"

                    Write-Host "Construyendo $frontendImage"

                    & $env:DOCKER_EXE build `
                        -f docker/frontend.Dockerfile `
                        -t $frontendImage .

                    if ($LASTEXITCODE -ne 0) {
                        throw "Fallo la construccion del frontend"
                    }
                '''
            }
        }

        stage('Verificar frontend') {
            steps {
                powershell '''
                    $ErrorActionPreference = "Stop"

                    $container = "bugistore-jenkins-frontend-$env:BUILD_NUMBER"
                    $image = "$env:DOCKERHUB_REPO/bugistore-frontend`:$env:IMAGE_TAG"

                    try {
                        & $env:DOCKER_EXE run -d `
                            --name $container `
                            --network gestion-desarrollo-software-equipo01_backend `
                            -p 18082:80 $image

                        if ($LASTEXITCODE -ne 0) {
                            throw "No se pudo iniciar el contenedor frontend"
                        }

                        $ready = $false

                        for ($i = 0; $i -lt 15; $i++) {
                            Start-Sleep -Seconds 2

                            try {
                                $response = Invoke-WebRequest `
                                    -Uri "http://localhost:18082" `
                                    -UseBasicParsing `
                                    -TimeoutSec 5

                                if ($response.StatusCode -eq 200) {
                                    $ready = $true
                                    break
                                }
                            }
                            catch {
                                Write-Host "Esperando al frontend..."
                            }
                        }

                        if (-not $ready) {
                            throw "El frontend no respondio correctamente"
                        }

                        Write-Host "Frontend verificado: HTTP 200"
                    }
                    finally {
                        & $env:DOCKER_EXE rm -f $container
                    }
                '''
            }
        }

        stage('Publicar en Docker Hub') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub-credentials',
                        usernameVariable: 'DOCKERHUB_USER',
                        passwordVariable: 'DOCKERHUB_TOKEN'
                    )
                ]) {
                    powershell '''
                        $ErrorActionPreference = "Stop"

                        # DIAGNOSTICO SEGURO DE LA CREDENCIAL
                        Write-Host "======================================"
                        Write-Host "DIAGNOSTICO DE CREDENCIAL DOCKER HUB"
                        Write-Host "======================================"

                        Write-Host "Usuario Docker Hub: $env:DOCKERHUB_USER"
                        Write-Host "Longitud del token: $($env:DOCKERHUB_TOKEN.Length)"
                        Write-Host "Prefijo correcto: $($env:DOCKERHUB_TOKEN.StartsWith('dckr_pat_'))"

                        # HUELLA SHA-256 SIN MOSTRAR EL TOKEN
                        $bytes = [System.Text.Encoding]::UTF8.GetBytes(
                            $env:DOCKERHUB_TOKEN
                        )

                        $sha = [System.Security.Cryptography.SHA256]::Create()

                        try {
                            $hashBytes = $sha.ComputeHash($bytes)
                            $hash = [BitConverter]::ToString($hashBytes).Replace("-", "")
                            Write-Host "Huella SHA-256 Jenkins: $hash"
                        }
                        finally {
                            $sha.Dispose()
                        }

                        Write-Host "======================================"

                        # DIAGNOSTICO DEL ENTORNO DOCKER
                        Write-Host "DIAGNOSTICO DEL ENTORNO DOCKER"
                        Write-Host "======================================"

                        Write-Host "Usuario Windows:"
                        whoami

                        Write-Host "USERPROFILE: $env:USERPROFILE"
                        Write-Host "DOCKER_CONFIG: $env:DOCKER_CONFIG"
                        Write-Host "DOCKER_HOST: $env:DOCKER_HOST"
                        Write-Host "DOCKER_CONTEXT: $env:DOCKER_CONTEXT"

                        Write-Host "Version de Docker:"
                        & $env:DOCKER_EXE version

                        if ($LASTEXITCODE -ne 0) {
                            throw "No se pudo consultar Docker"
                        }

                        Write-Host "Contexto Docker:"
                        & $env:DOCKER_EXE context show

                        if ($LASTEXITCODE -ne 0) {
                            throw "No se pudo consultar el contexto Docker"
                        }

                        Write-Host "======================================"

                        # INICIAR SESION EN DOCKER HUB CON ENTRADA UTF-8
                        Write-Host "Iniciando sesion en Docker Hub mediante UTF-8..."

                        $startInfo = New-Object System.Diagnostics.ProcessStartInfo
                        $startInfo.FileName = $env:DOCKER_EXE
                        $startInfo.Arguments = "login --username `"$env:DOCKERHUB_USER`" --password-stdin"
                        $startInfo.UseShellExecute = $false
                        $startInfo.RedirectStandardInput = $true
                        $startInfo.RedirectStandardOutput = $true
                        $startInfo.RedirectStandardError = $true

                        $process = New-Object System.Diagnostics.Process
                        $process.StartInfo = $startInfo

                        try {
                            $null = $process.Start()

                            $stdoutTask = $process.StandardOutput.ReadToEndAsync()
                            $stderrTask = $process.StandardError.ReadToEndAsync()

                            $tokenBytes = [System.Text.Encoding]::UTF8.GetBytes(
                                $env:DOCKERHUB_TOKEN + "`n"
                            )

                            try {
                                $process.StandardInput.BaseStream.Write(
                                    $tokenBytes,
                                    0,
                                    $tokenBytes.Length
                                )

                                $process.StandardInput.BaseStream.Flush()
                            }
                            finally {
                                [Array]::Clear(
                                    $tokenBytes,
                                    0,
                                    $tokenBytes.Length
                                )

                                $process.StandardInput.Close()
                            }

                            $process.WaitForExit()

                            $stdout = $stdoutTask.Result
                            $stderr = $stderrTask.Result

                            if ($stdout) {
                                Write-Host $stdout
                            }

                            if ($stderr) {
                                Write-Host $stderr
                            }

                            if ($process.ExitCode -ne 0) {
                                throw "Error al iniciar sesion en Docker Hub. Codigo: $($process.ExitCode)"
                            }

                            Write-Host "Sesion de Docker Hub iniciada correctamente."
                        }
                        finally {
                            $process.Dispose()
                        }

                        # PUBLICAR LAS SEIS IMAGENES
                        try {
                            $services = @(
                                "auth-service",
                                "user-service",
                                "game-service",
                                "purchase-service",
                                "api-gateway",
                                "frontend"
                            )

                            foreach ($service in $services) {
                                $image = "$env:DOCKERHUB_REPO/bugistore-$service`:$env:IMAGE_TAG"

                                Write-Host "Publicando $image"

                                & $env:DOCKER_EXE push $image

                                if ($LASTEXITCODE -ne 0) {
                                    throw "Fallo la publicacion de $image"
                                }
                            }

                            Write-Host "Las seis imagenes se publicaron correctamente."
                        }
                        finally {
                            Write-Host "Cerrando sesion de Docker Hub..."

                            & $env:DOCKER_EXE logout

                            if ($LASTEXITCODE -ne 0) {
                                Write-Warning "No se pudo cerrar la sesion de Docker Hub."
                            }
                        }
                    '''
                }
            }
        }
    }

    post {
        success {
            echo 'Pipeline BugiStore completado correctamente.'
            echo 'Las seis imagenes se publicaron con su etiqueta Jenkins.'
        }

        failure {
            echo 'El pipeline fallo. Revisa la consola para identificar la etapa.'
        }

        always {
            echo 'Finalizo la ejecucion del pipeline BugiStore.'
        }
    }
}
