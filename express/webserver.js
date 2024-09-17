const express = require('express');
const phpExpress = require('php-express')({
  binPath: '/opt/homebrew/bin/php'
});
const session = require('express-session');
const FileStore = require('session-file-store')(session);
const path = require('path');

const app = express();

// Set view engine to PHP
app.engine('php', phpExpress.engine);
app.set('view engine', 'php');

// Specify the directory where your PHP files are located
app.set('views', __dirname);

// Session configuration
const sessionConfig = {
  store: new FileStore({
    path: path.join(__dirname, 'sessions'),
    ttl: 3600, // Session TTL in seconds (1 hour)
    retries: 0,
    secret: 'LR9YWPFBJENXKQZM4H5C2T'
  }),
  secret: 'LR9YWPFBJENXKQZM4H5C2T',
  resave: false,
  saveUninitialized: false,
  cookie: {
    maxAge: 3600000, // 1 hour in milliseconds
    httpOnly: true,
    secure: false, // Set to true if using HTTPS
    sameSite: 'lax'
  },
  name: 'sessionId' // Custom name for the session ID cookie
};

// Use session middleware
app.use(session(sessionConfig));

// Parse URL-encoded bodies (as sent by HTML forms)
app.use(express.urlencoded({ extended: true }));

// Middleware to check session expiration
app.use((req, res, next) => {
  if (req.session && req.session.cookie && req.session.cookie.expires) {
    if (new Date() > req.session.cookie.expires) {
      req.session.destroy((err) => {
        if (err) console.error('Error destroying session:', err);
      });
    }
  }
  next();
});

// Route all .php files to PHP engine BEFORE serving static files
app.all('*.php', phpExpress.router);

// Serve static files (including HTML)
app.use(express.static(path.join(__dirname, '/')));

// Serve index.php as the root route
app.get('/', (req, res) => {
  res.render('index.php');
});

// Example of setting a session variable
app.get('/set-session', (req, res) => {
  req.session.username = 'testuser';
  res.send('Session variable set');
});

// Example of getting a session variable
app.get('/get-session', (req, res) => {
  res.send(`Username: ${req.session.username || 'Not set'}`);
});

// Example of destroying a session
app.get('/destroy-session', (req, res) => {
  req.session.destroy((err) => {
    if (err) {
      res.status(500).send('Error destroying session');
    } else {
      res.send('Session destroyed');
    }
  });
});

const PORT = 3000;

app.listen(PORT, () => {
  console.log(`Node server listening on port ${PORT}!`);
}).on('error', (err) => {
  console.error('Error starting server:', err);
});

/**
const express = require('express');
const phpExpress = require('php-express')({
  binPath: '/opt/homebrew/bin/php'
});
const session = require("express-session");
const path = require('path');

const app = express();

// Set view engine to PHP
app.engine('php', phpExpress.engine);
app.set('view engine', 'php');

// Specify the directory where your PHP files are located
app.set('views', __dirname);

// Parse URL-encoded bodies (as sent by HTML forms)
app.use(
    session({
      secret: "LR9YWPFBJENXKQZM4H5C2T",
      resave:false,
      saveUninitialized: false,
    }),
  express.urlencoded({ extended: true }),
);


var hour = 3600000;
req.session.cookie.expires = new Date(Date.now() + hour);
req.session.cookie.maxAge = hour;

app.use(session({ secret: 'LR9YWPFBJENXKQZM4H5C2T', cookie: { maxAge: 40000 }}))

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
**/