const express = require('express');
const cors = require('cors');

const app = express();
const PORT = 3001;

app.use(cors());

// Rota de teste
app.get('/', (req, res) => {
  res.send('API backend funcionando!');
});

app.listen(PORT, () => {
  console.log(`Servidor rodando em http://localhost:${PORT}`);
});
