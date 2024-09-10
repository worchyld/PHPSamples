const express = require('express');
const phpExpress = require('php-express')({
  binPath: '/opt/homebrew/bin/php'
});
const path = require('path');

const app = express();

// Set view engine to PHP
app.engine('php', phpExpress.engine);
app.set('view engine', 'php');

// Specify the directory where your PHP files are located
app.set('views', __dirname);

// Parse URL-encoded bodies (as sent by HTML forms)
app.use(express.urlencoded({ extended: true }));

// Route all .php files to PHP engine BEFORE serving static files
app.all('*.php', phpExpress.router);


// Serve static files (including HTML)
app.use(express.static(path.join(__dirname, '/')));

// Serve index.php as the root route
app.get('/', (req, res) => {
  res.render('index.php');
});

// Your other routes and middleware here

const PORT = 3000;

app.listen(PORT, () => {
  console.log(`Node server listening on port ${PORT}!`);
}).on('error', (err) => {
  console.error('Error starting server:', err);
});