The following file will describe the folders used in Direct's folder structure.

Please ensure to export the shared APIs from the api repository, i.e. svn://localhost/php5api/tags/<CURRENT VERSION> and place the exported folder structure at the same level as the application’s folder:
D:\www\php5api - Top folder for all Shared APIs
D:\www\application - Application's top folder
It's important to check what the highest available version of this API is before doing an export.
This can be done using the "Browse Repository" option from the Tortoise SVN client.

api - Holds the files for the underlying api that the applications uses
api/classes - Holds the class files for the underlying application api
api/classes/general.php - General class for functionality methods which are used by several different modules or components
api/claases/websession.php - API class for handling all Web Sessions using an Object to store session info
api/functions - Holds the function files for the uderlying application api
api/functions/global.php - Global file for all functions used by multiple modules and components
api/interfaces - Holds the interface files for the uderlying application api if applicable

conf - Holds all application configuration files
conf/global.php - Global configuration file in the php format, includes configuration for:
	- Connecting to the Application Database
	- Connection to the Session Database
	- Connection to the iEmendo error repository
	- Sets the error reporting level
	- Defines the Log Path constants
	- Defines the Debug Level Constant
	- Defines the Output Method constant

db - holds all database related SQL scripts
db/master_<database>_v<version number>.sql - Master SQL file for creating or upgrading the database to the applicable version
db/setup_<database>_v<version number>.sql - SQL file for populating the database with setup data for the version
db/test_<database>_v<version number>.sql - SQL file for populating the database with test data for the version

doc - Holds all documentation for the application including:
	- specification
	- surveillance manual
	- user manual
	- test documents for each test cycle
	- flowcharts
	- data flowcharts
	- database diagrams
	- description for public apis
	- etc.

webroot - Document root for the Web Server
webroot/inc - Holds all include files that are used by multiple pages such as include.php
webroot/inc/include.php - Global include file for including the share API as well as:
	- The General Application API classes
	- The Global Application API functions
	- The Global Application config file
	- The Global Application text files
webroot/text - Holds all text files used by different modules and components, text files should be in Cydev's "Translate Text" format
webroot/text/<language> - Holds all text files for a specific language
webroot/home - Holds all files for creating the basic login pages and authenticating the user upon login
webroot/_test - Holds dummy test files for each component
webroot/css - Holds Cascading StyleSheet (CSS) files
webroot/template - Holds template files for each available template
webroot/template/<template> - Holds the template files for each of the components available in the template
webroot/template/<template>/<component> - Holds the xsl files used for rendering each of data component's data files to the specified template,
webroot/template/<template>/<component>/<function>.xsl - Holds XSL template files for generating the Application's GUI.
webroot/<component> - Holds the controller and XML data files for the component
