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

        const completion = await openai.chat.completions.create({
            model: "gpt-3.5-turbo",
            messages: [
                {
                    role: "system",
                    content: `Du bist ein erfahrener Blogger, der Blogbeiträge in deutscher Sprache schreibt. 
                    Dein Stil ist freundlich, persönlich und unterhaltsam, jedoch immer informativ und gut strukturiert. 
                    Du verwendest eine klare, leicht verständliche Sprache und achtest auf Rechtschreibung und Grammatik.`
                },
                {
                    role: "user",
                    content: `Schreibe einen kurzen deutschen Blogpost über "${topic}". 
                    Gliedere den Text in maximal drei Absätze. 
                    Der Beitrag soll:
                    1. Mit einem kurzen, ansprechenden Einleitungssatz starten.
                    2. Im Hauptteil wesentliche Informationen oder Tipps zum Thema enthalten.
                    3. Mit einem Mini-Fazit oder einer persönlichen Note enden.
                    
                    Halte den Text locker, aber informativ. 
                    Vermeide zu viele Fachbegriffe und bleibe bei insgesamt maximal 3 Absätzen.`
                }
            ],
            temperature: 0.7,
            max_tokens: 500
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