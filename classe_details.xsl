<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <div class="container mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h2 class="h4 mb-0">
                                Classe <xsl:value-of select="classe/@filiere"/> - Niveau <xsl:value-of select="classe/@niveau"/>
                            </h2>
                        </div>
                        <div class="card-body">
                            <!-- Liste des étudiants -->
                            <h3 class="h5 mb-3">Liste des étudiants</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>N° Inscription</th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <xsl:apply-templates select="classe/etudiants/etudiant"/>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Liste des modules -->
                            <h3 class="h5 mb-3 mt-4">Modules enseignés</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Code Module</th>
                                            <th>Nom du Module</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <xsl:apply-templates select="classe/modules/module"/>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </xsl:template>
    
    <xsl:template match="etudiant">
        <tr>
            <td><xsl:value-of select="@numInscription"/></td>
            <td><xsl:value-of select="@nom"/></td>
            <td><xsl:value-of select="@prenom"/></td>
        </tr>
    </xsl:template>
    
    <xsl:template match="module">
        <tr>
            <td><span class="badge bg-primary"><xsl:value-of select="@idModule"/></span></td>
            <td><xsl:value-of select="@nomModule"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet> 