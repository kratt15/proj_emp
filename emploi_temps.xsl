<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <div class="table-responsive">
            <h3 class="mb-3">
                Emploi du temps - Classe <xsl:value-of select="emploi/@classe"/>
            </h3>
            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Jour</th>
                        <th>Horaire</th>
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