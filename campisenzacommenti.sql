SELECT
    c.TABLE_NAME AS Tabella,
    c.COLUMN_NAME AS Campo_Senza_Commento
FROM
    information_schema.COLUMNS c
INNER JOIN
    information_schema.TABLES t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME
WHERE
    c.TABLE_SCHEMA = DATABASE() -- Sostituisci con 'nome_database' se non hai un DB selezionato
    AND t.TABLE_TYPE = 'BASE TABLE' -- Esclude le Viste (Views)
    AND (c.COLUMN_COMMENT IS NULL OR c.COLUMN_COMMENT = '') -- Solo campi senza commento
    AND c.COLUMN_NAME NOT IN ('id','created_at', 'updated_at', 'deleted_at') -- Esclude i campi di sistema
 AND c.COLUMN_NAME NOT LIKE '%\_id' -- ESCLUDE I CAMPI CHE FINISCONO CON _id
    AND NOT EXISTS (
        -- Questa subquery verifica che il campo NON sia una Foreign Key
        SELECT 1
        FROM information_schema.KEY_COLUMN_USAGE kcu
        WHERE kcu.TABLE_SCHEMA = c.TABLE_SCHEMA
          AND kcu.TABLE_NAME = c.TABLE_NAME
          AND kcu.COLUMN_NAME = c.COLUMN_NAME
          AND kcu.REFERENCED_TABLE_NAME IS NOT NULL -- Se c'è una tabella referenziata, è una FK
    )
ORDER BY
    c.TABLE_NAME,
    c.COLUMN_NAME;
