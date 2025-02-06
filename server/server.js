const express = require('express');
const cors = require('cors');
const { OpenAI } = require('openai');
require('dotenv').config();

// Überprüfe, ob der API Key vorhanden ist
if (!process.env.OPENAI_API_KEY) {
    console.error('FEHLER: OPENAI_API_KEY ist nicht in der .env Datei definiert!');
    process.exit(1);
}

const app = express();
const port = process.env.PORT || 3000;

// OpenAI Konfiguration
const openai = new OpenAI({
    apiKey: process.env.OPENAI_API_KEY
});

// CORS-Konfiguration
app.use(cors({
    origin: 'https://chill-zone.xyz',
    methods: ['GET', 'POST', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Accept']
}));

// Body Parser
app.use(express.json());

// Health Check Endpoint
app.get('/health', (req, res) => {
    res.json({ status: 'ok' });
});

// Blog Post Generator Endpoint
app.post('/blog/generate-blog', async (req, res) => {
    try {
        const { topic } = req.body;
        
        if (!topic) {
            return res.status(400).json({
                success: false,
                message: 'Kein Thema angegeben'
            });
        }

        console.log('Generiere Blog Post für Thema:', topic);

        const completion = await openai.createChatCompletion({
            model: "gpt-4",
            messages: [
              // System Prompt
              {
                role: "system",
                content: `
                Du bist ein professioneller Blogger und erfahrener Software-Entwickler. 
                Du schreibst Blogposts auf Deutsch, verwendest eine klare und leicht verständliche Sprache 
                und kannst bei Bedarf funktionierende Code-Beispiele einbinden. 
                Achte auf Stil und Struktur, sodass deine Texte sowohl informativ als auch gut lesbar sind. 
                Verwende korrekte Syntax und füge ggf. Erklärungen zu deinem Code hinzu.`
              },
              // User Prompt
              {
                role: "user",
                content: `
                Bitte schreibe einen kurzen Blogpost (max. 3 Absätze) über "${topic}", 
                in dem du **auch ein kurzes Code-Beispiel** präsentierst und erklärst. 
                
                ### Vorgaben:
                1. **Thema**: "${topic}"
                2. **Aufbau**:
                   - Absatz 1: Kurzer, einleitender Überblick zum Thema (ca. 50–70 Wörter).
                   - Absatz 2: Stelle ein Code-Beispiel vor (in beliebiger Programmiersprache, sofern passend), 
                     inkl. kurzer Erklärung, was der Code macht und warum er nützlich ist.
                   - Absatz 3: Fasse die wichtigsten Punkte zusammen und gib einen kleinen Ausblick, 
                     z. B. wo oder wie man den Code erweitern kann.
                3. **Länge**: Maximal 3 Absätze, jeder Absatz sollte zwischen 40 und 80 Wörtern umfassen.
                4. **Stil**: Freundlich, persönlich, leicht verständlich, du-Ansprache.
                5. **Code-Format**: Bitte verwende Markdown für den Codeblock, z. B.:
                   \`\`\`js
                   // Beispiel-Code
                   console.log("Hello World");
                   \`\`\`
                6. **Ziel**: Einsteigerinnen und Einsteiger sollen den Text gut verstehen und den Code leicht nachvollziehen können.
                
                Verfasse den gesamten Text auf Deutsch. Achte auf Rechtschreibung und Grammatik.`
              }
            ],
            temperature: 0.7,
            max_tokens: 800
          });
      

        const generatedContent = completion.choices[0].message.content.trim();
        console.log('Generierter Content:', generatedContent.substring(0, 100) + '...');

        res.json({
            success: true,
            post: {
                title: topic,
                content: generatedContent
            }
        });

    } catch (error) {
        console.error('Fehler beim Generieren des Blog Posts:', error);
        res.status(500).json({
            success: false,
            message: 'Fehler beim Generieren des Blogposts: ' + (error.message || 'Unbekannter Fehler')
        });
    }
});

// Error Handler
app.use((err, req, res, next) => {
    console.error('Server Error:', err);
    res.status(500).json({
        success: false,
        message: 'Interner Server-Fehler'
    });
});

// Server starten
app.listen(port, '127.0.0.1', () => {
    console.log(`Server läuft auf http://127.0.0.1:${port}`);
});