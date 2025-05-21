<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="text" encoding="utf-8"/>

  <xsl:template match="/">
    <xsl:for-each select="/emploi/seance">
      <card>
        <p>
          <xsl:value-of select="concat('Jour: ', jour)"/>
        </p>
        <p>
          <xsl:value-of select="concat('Début: ', debut)"/>
        </p>
        <p>
          <xsl:value-of select="concat('Fin: ', fin)"/>
        </p>
        <p>
          <xsl:value-of select="concat('Professeur: ', prof)"/>
        </p>
        <p>
          <xsl:value-of select="concat('Module: ', module)"/>
        </p>
        <p>
          <xsl:value-of select="concat('Salle: ', salle)"/>
        </p>
      </card>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>