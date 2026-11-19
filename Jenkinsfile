
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

                    Write-Host "Las seis imagenes fueron construidas correctamente."
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
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub-credentials',
                    usernameVariable: 'DH_USER',
                    passwordVariable: 'DH_PAT'
                )]) {
                    powershell '''
                        $ErrorActionPreference = "Stop"

                        $docker = $env:DOCKER_EXE
                        $tempConfig = Join-Path $env:WORKSPACE ".docker-auth-$env:BUILD_NUMBER"

                        $bytes = $null
                        $pair = $null
                        $auth = $null

                        New-Item -ItemType Directory -Force $tempConfig | Out-Null

                        try {
                            Write-Host "======================================"
                            Write-Host "AUTENTICACION TEMPORAL DOCKER HUB"
                            Write-Host "======================================"

                            Write-Host "Usuario: $env:DH_USER"
                            Write-Host "Preparando credenciales..."

                            $pair = "$($env:DH_USER):$($env:DH_PAT)"
                            $bytes = [System.Text.Encoding]::UTF8.GetBytes($pair)
                            $auth = [Convert]::ToBase64String($bytes)

                            $config = @{
                                auths = @{
                                    "https://index.docker.io/v1/" = @{
                                        auth = $auth
                                    }
                                }
                            } | ConvertTo-Json -Depth 5

                            [System.IO.File]::WriteAllText(
                                (Join-Path $tempConfig "config.json"),
                                $config,
                                [System.Text.UTF8Encoding]::new($false)
                            )

                            $env:DOCKER_CONFIG = $tempConfig

                            Write-Host "Autenticacion temporal preparada correctamente."
                            Write-Host "======================================"
                            Write-Host "PUBLICANDO IMAGENES"
                            Write-Host "======================================"

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

                                & $docker push $image

                                if ($LASTEXITCODE -ne 0) {
                                    throw "Fallo la publicacion de $image"
                                }

                                Write-Host "Publicacion completada: $image"
                            }

                            Write-Host "======================================"
                            Write-Host "LAS SEIS IMAGENES SE PUBLICARON CORRECTAMENTE"
                            Write-Host "======================================"
                        }
                        finally {
                            $pair = $null
                            $auth = $null

                            if ($null -ne $bytes) {
                                [Array]::Clear($bytes, 0, $bytes.Length)
                            }

                            Remove-Item $tempConfig -Recurse -Force -ErrorAction SilentlyContinue

                            $env:DOCKER_CONFIG = "C:\\ProgramData\\Jenkins\\.docker"

                            Write-Host "Configuracion temporal eliminada."
                        }
                    '''
                }
            }
        }
    }

    post {
        success {
            echo 'Pipeline BugiStore completado correctamente.'
            echo 'Las seis imagenes fueron publicadas en Docker Hub.'
        }

        failure {
            echo 'El pipeline fallo. Revisa la consola para identificar la etapa.'
        }

        always {
            echo 'Finalizo la ejecucion del pipeline BugiStore.'
        }
    }
}
