#mPoint

##Structure

api - Holds the files for the underlying api that the applications uses.  
api/classes - Holds the class files for the underlying application api.  
api/classes/general.php - General class for functionality methods which are used by several different modules or components.  
api/classes/websession.php - API class for handling all Web Sessions using an Object to store session info.  
api/functions - Holds the function files for the uderlying application api.  
api/functions/global.php - Global file for all functions used by multiple modules and components.  
api/interfaces - Holds the interface files for the uderlying application api if applicable.  
conf - Holds all application configuration files.  

conf/global.php - (SET VALUES WITH ENV VARS) Global configuration file in the php format, includes configuration for:
* Connecting to the Application Database
* Connection to the Session Database
* Connection to the iEmendo error repository
* Sets the error reporting level
* Defines the Log Path constants
* Defines the Debug Level Constant
* Defines the Output Method constant

db - holds all database related SQL scripts (DEPRECATED, SEE /env).  
db/master_<database>_v<version number>.sql - Master SQL file for creating or upgrading the database to the applicable version.  
db/setup_<database>_v<version number>.sql - SQL file for populating the database with setup data for the version.  
db/test_<database>_v<version number>.sql - SQL file for populating the database with test data for the version

doc - Holds all documentation for the application including:
* specification
* surveillance manual
* user manual
* test documents for each test cycle
* flowcharts
* data flowcharts
* database diagrams
* description for public apis
* etc.

docker - docker related files.  
env - Contains client configurations for deploying on various environments.  
liquibase - Contains core schema and core data to be versioned and migrated by liquibase.    
webroot - Document root for the Web Server.  
webroot/inc - Holds all include files that are used by multiple pages such as include.php

webroot/inc/include.php - Global include file for including the share API as well as:
* The General Application API classes
* The Global Application API functions
* The Global Application config file
* The Global Application text files

webroot/text - Holds all text files used by different modules and components, text files should be in Cydev's "Translate Text" format.  
webroot/text/<language> - Holds all text files for a specific language.  
webroot/home - Holds all files for creating the basic login pages and authenticating the user upon login.  
webroot/_test - Holds dummy test files for each component.  
webroot/css - Holds Cascading StyleSheet (CSS) files.  
webroot/template - Holds template files for each available template.  
webroot/template/<template> - Holds the template files for each of the components available in the template.  
webroot/template/<template>/<component> - Holds the xsl files used for rendering each of data component's data files to the specified template,.  
webroot/template/<template>/<component>/<function>.xsl - Holds XSL template files for generating the Application's GUI..  
webroot/<component> - Holds the controller and XML data files for the component


##Run locally with xdebug enabled and volumed codebase for easy debugging and development
1. Rename or copy .env.example to .env in root
2. Set preferred envs or use defaults
3. Volume local client setups in alphanumeric order in docker-compose file to liquibase:/app/scripts/sql (see comment).  
4. Run docker-compose up --build
5. In Intellij open Preferences / Languages & Frameworks / PHP / Servers and add a server called mpoint.local.cellpointmobile.com and setup correct pathmapping.  
6. Enable php debug listener, set checkpoints and run request.

## Run locally with final image and all unit testing
1. Rename or copy .env.example to .env in root
2. Set preferred envs or use defaults
3. Volume local client setups in alphanumeric order in docker-compose file to liquibase:/app/scripts/sql (see comment).
4. Change in docker-compose file in app service: comment out all volumes.
5. Change in docker-compose file in app service: "context: docker/php-fpm-alpine-debug" to "context: .".  
6. Run docker-compose up --build.





