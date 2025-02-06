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
                Du bist ein professioneller Blogger mit langjähriger Erfahrung. 
                Du schreibst alle Texte auf Deutsch, achtest auf eine leicht verständliche Sprache 
                und einen freundlichen, persönlichen Ton. 
                Du stellst sicher, dass deine Texte sowohl unterhaltsam als auch informativ sind, 
                ohne zu ausschweifend zu werden. 
                Du bleibst beim Thema und verzichtest auf allzu allgemeine Floskeln. 
                Fehlerfreie Rechtschreibung und Grammatik sind selbstverständlich.
                `
              },
              // User Prompt
              {
                role: "user",
                content: `
                Bitte schreibe einen kurzen Blogpost über das Thema "${topic}".
                
                Folgende Vorgaben gelten für diesen Blogpost:
                
                1. **Länge**: Maximal 3 Absätze, jeder Absatz sollte zwischen 50 und 80 Wörtern umfassen.
                2. **Struktur**:
                   - **Einleitung**: Beginne mit einem kurzen, packenden Satz, der neugierig macht.
                   - **Hauptteil**: Erläutere das Thema mit 1–2 konkreten Beispielen oder Tipps.
                   - **Fazit**: Schließe den Beitrag mit einem kurzen Resümee oder einer persönlichen Note ab.
                3. **Stil**:
                   - Schreibe in einem lockeren, persönlichen Ton (Du-Ansprache).
                   - Achte auf einen freundlichen und zugänglichen Sprachstil, ohne zu viele Fachbegriffe.
                   - Wenn Fachbegriffe notwendig sind, erläutere sie kurz.
                4. **Zusätzliche Hinweise**:
                   - Binde nach Möglichkeit eine kleine Anekdote oder eine kurze Geschichte ein, um den Text aufzulockern.
                   - Vermeide zu allgemeine Aussagen, werde konkret beim Thema.
                   - Nutze eine ansprechende Überschrift (als erste Zeile).
                   - Verwende keine klassischen Schlussformeln wie „Abschließend lässt sich sagen…“, sondern bleibe kreativ. 
                5. **Zielgruppe**: Menschen, die sich schnell und unterhaltsam über dieses Thema informieren möchten.
                
                Erfülle bitte alle o. g. Vorgaben. 
                Schreibe ausschließlich auf Deutsch. 
                `
              }
            ],
            temperature: 0.7,
            max_tokens: 600
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