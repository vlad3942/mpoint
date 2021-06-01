@Library('build-library') _
//This build needs extra ressources (highRes param enabled) as it utilizes running liquibase as part of the docker build.
//Signature: call( String upstreamProjects = "", String extraDockerfiles = "",  boolean pushImage = true, boolean highRes = false )
DockerKubernetesBuild("CTECH/phpfpmextras/master", "Dockerfile.liquibase", true, true)
