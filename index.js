const express = require('express'); // Obviously, we need Express.
const pug = require('pug'); // And we couldn't render Pug without Pug.
require('jstransformer-markdown-it'); // This needs to be installed for Pug to use it.

const app = express();
app.use(express.static('public'));

app.get('/', function (req, res) {
    res.send(pug.renderFile('public/index.pug')); // We use pug.renderFile to render a file.
});

// Add more URLs and Pug files here...

app.listen(8080, function() {
    console.log('Server up!');
});