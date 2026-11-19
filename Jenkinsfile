
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

        stage('Prueba Docker Push sin Login') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub-credentials',
                    usernameVariable: 'DH_USER',
                    passwordVariable: 'DH_PAT'
                )]) {
                    powershell '''
                        $ErrorActionPreference = "Stop"

                        $docker = $env:DOCKER_EXE
                        $tempConfig = Join-Path $env:WORKSPACE ".docker-auth-test"
                        $bytes = $null
                        $pair = $null
                        $auth = $null

                        New-Item -ItemType Directory -Force $tempConfig | Out-Null

                        try {
                            Write-Host "Preparando autenticacion temporal..."

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

                            Write-Host "Configuracion temporal preparada."
                            Write-Host "Probando publicacion de una imagen..."

                            $image = "$env:DOCKERHUB_REPO/bugistore-auth-service`:$env:IMAGE_TAG"

                            & $docker push $image

                            if ($LASTEXITCODE -ne 0) {
                                throw "El push de prueba fallo. Codigo: $LASTEXITCODE"
                            }

                            Write-Host "RESULTADO: Docker publico la imagen correctamente."
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

        stage('Publicar en Docker Hub') {
            when {
                expression { false }
            }
            steps {
                echo 'Publicacion normal desactivada temporalmente durante el diagnostico.'
            }
        }
    }

    post {
        success {
            echo 'Pruebas y diagnostico completados correctamente.'
        }

        failure {
            echo 'El pipeline fallo. Revisa la consola para identificar la etapa.'
        }

        always {
            echo 'Finalizo la ejecucion del pipeline BugiStore.'
        }
    }
}
