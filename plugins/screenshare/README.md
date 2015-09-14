Follow the below given steps to sign the jar file:
1. Download the pfx file provided
2. keytool -importkeystore -srckeystore certificate.pfx -srcstoretype pkcs12
3. keytool -changealias -alias {KEY_ID} -destalias inscripts ({KEY_ID} is the one provided by the previous command)
4. keytool -list (Verify that the key is added)
5. jarsigner ScreenShare.jar inscripts (signs the jar with the key)
6. jarsigner -verify ScreenShare.jar inscripts (verifies that the jar is signed properly)

Ensure the following code exists after signing the ScreenShare.jar file:
1. com directory
2. screenshare directory
3. image files
4. META-INF directory that contains 3 files i.e. Manifest.mf and 2 code signing files.
5. Application Name and Permissions attribute in Manifest file