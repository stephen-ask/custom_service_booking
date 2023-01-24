*****************************************************
*					BUILD RELEASES					*
*****************************************************

1)	Create a new file with name "build.properties" 
2)  Add the next info to the file
 
## the destination folder to release
release.folder=D:/php/sdk/releases
## the base name to release
release.name=payu-php-sdk
## the release extension
release.extension=zip

3)	Run the build.xml and verify the file generation, you have two alternatives to run the build.xml:
	3.1) If you have the project on eclipse right click over build.xml file select "run as" and select "Ant Build"
	3.2) If you want use the console just use the ant command i.e 
	     ANT_HOME/ant -buildfile "path-to-project/build.xml"
	
	