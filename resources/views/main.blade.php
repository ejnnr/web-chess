<!DOCTYPE html>
<html>
	<head>
		<title>WebChess</title>
		<meta charset="UTF8">
        <script src="lib/angular2-polyfills.js"></script>
        <script src="lib/system.src.js"></script>
        <script src="lib/Rx.js"></script>
        <script src="lib/angular2.dev.js"></script>

        <script>
            System.config({
                packages: {        
                    js: {
                        format: 'register',
                        defaultExtension: 'js'
                    }
                }
            });
            System.import('js/boot')
                .then(null, console.error.bind(console));
        </script>
	</head>
	<body>
        <web-chess>Loading...</web-chess>
	</body>
</html>

