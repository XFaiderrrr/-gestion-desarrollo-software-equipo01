
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

        stage('Construir imágenes Docker') {
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
                            throw "Falló la construcción de $service"
                        }
                    }

                    $frontendImage = "$env:DOCKERHUB_REPO/bugistore-frontend`:$env:IMAGE_TAG"

                    & $env:DOCKER_EXE build `
                        -f docker/frontend.Dockerfile `
                        -t $frontendImage .

                    if ($LASTEXITCODE -ne 0) {
                        throw "Falló la construcción del frontend"
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
                            throw "El frontend no respondió correctamente"
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

                        try {
                            $env:DOCKERHUB_TOKEN |
                                & $env:DOCKER_EXE login `
                                    --username $env:DOCKERHUB_USER `
                                    --password-stdin

                            if ($LASTEXITCODE -ne 0) {
                                throw "Error al iniciar sesión en Docker Hub"
                            }

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
                                    throw "Falló la publicación de $image"
                                }
                            }
                        }
                        finally {
                            & $env:DOCKER_EXE logout
                        }
                    '''
                }
            }
        }
    }

    post {
        success {
            echo '¡Pipeline BugiStore completado correctamente!'
            echo 'Las seis imágenes se publicaron con su etiqueta Jenkins.'
        }

        failure {
            echo 'El pipeline falló. Revisa la consola para identificar la etapa.'
        }

        always {
            echo 'Finalizó la ejecución del pipeline BugiStore.'
        }
    }
}
