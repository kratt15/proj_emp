<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 5.0//EN" indent="yes"/>
    
    <xsl:template match="/">
        <html lang="fr">
            <head>
                <meta charset="UTF-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <title>Emploi du temps - <xsl:value-of select="emploi/@classe"/></title>
                <!-- Bootstrap CSS -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
                <style>
                    .table-responsive {
                        margin-top: 2rem;
                    }
                    .card {
                        margin-bottom: 2rem;
                    }
                    .badge {
                        font-size: 0.9rem;
                    }
                </style>
            </head>
            <body>
                <div class="container py-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h1 class="h3 mb-0 text-center">
                                Emploi du temps - Classe <xsl:value-of select="emploi/@classe"/>
                            </h1>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Jour</th>
                                            <th>Horaires</th>
                                            <th>Module</th>
                                            <th>Professeur</th>
                                            <th>Salle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <xsl:apply-templates select="emploi/seance">
                                            <xsl:sort select="@jour"/>
                                            <xsl:sort select="@debut"/>
                                        </xsl:apply-templates>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bootstrap JS -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            </body>
        </html>
    </xsl:template>
    
    <xsl:template match="seance">
        <tr>
            <td>
                <span class="badge bg-secondary text-capitalize">
                    <xsl:value-of select="@jour"/>
                </span>
            </td>
            <td>
                <xsl:value-of select="@debut"/> - <xsl:value-of select="@fin"/>
            </td>
            <td>
                <span class="badge bg-primary">
                    <xsl:value-of select="@module"/>
                </span>
            </td>
            <td>
                <xsl:value-of select="@prof"/>
            </td>
            <td>
                <span class="badge bg-info">
                    <xsl:value-of select="@salle"/>
                </span>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet> 