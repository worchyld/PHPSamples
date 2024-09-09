
const express = require('express');
const phpExpress = require('php-express')({
  // Path to your PHP binary
  binPath: '/opt/homebrew/bin/php'
});

const path = require('path');
const app = express();

// Set view engine to PHP
app.engine('php', phpExpress.engine);
app.set('view engine', 'php');

// app.get('/', (req, res) => {
// 	res.render('index.php');
//   });
  
// Serve static files (including HTML)
app.use(express.static(path.join(__dirname, '/')));

// Specify the directory where your PHP files are located
app.set('views', __dirname + '/');

// Route all .php files to PHP engine
app.use(express.urlencoded({ extended: true }));
app.all('*.php', phpExpress.router);

// Your other routes and middleware here

const PORT = 3000;

app.listen(PORT, () => {
	console.log(`Node server listening on port ${PORT}!`);
  }).on('error', (err) => {
	console.error('Error starting server:', err);
  });

// app.listen(PORT, () => {
//   console.log(`Node server listening on port ${PORT}!`);
// });