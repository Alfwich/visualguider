import urllib2, json, os

def transformName(name):
  return "".join([ i for i in name.replace(u" ","-") if i.isalnum() or i == "-" ])

def get(d,k):
  if unicode(k) in d:
    return unicode(d[unicode(k)]).replace( u"\"", u"'" )
  return unicode("")

def checkImageExists(name):
  name = transformName( name )
  if os.path.isfile( "/home/webserver/www/visualguider/image/card/%s.jpg" % ( name ) ):
    return (u"%s"%name)
  return u""

def main():
  with open( "cards.sql", "w") as f:
    res = urllib2.urlopen("http://mtgjson.com/json/AllSets.json")
    setDict = json.load(res)
    hasWritten = False
    rarityMap = {
      "": "",
      "Common": "C",
      "Basic Land": "L",
      "Mythic Rare": "M",
      "Rare": "R",
      "Special": "S",
      "Uncommon": "U"
    }
    cards = []

    f.write( "DELETE from cards;\n" )

    for (k,v) in setDict.iteritems():
      if "cards" in v:
        for card in v["cards"]:
          card["set"] = k
          cards.append( card )

    f.write( "INSERT INTO cards ( `title`, `type`, `mana`, `set`, `rarity`, `img` ) VALUES\n" )

    for card in cards:
      if not hasWritten:
        hasWritten = True
      else:
        f.write(u",")
      entry = unicode( u" (\"{0}\",\"{1}\",\"{2}\",\"{3}\",\"{4}\",\"{5}\")\n").format(unicode(get(card,"name")), unicode("MTG"), get(card,"manaCost"), get(card,"set"), rarityMap[get(card,"rarity")], checkImageExists( get(card,"name") ) )
      f.write(entry.encode('utf8'))
    f.write( ";" )



if __name__ == "__main__":
  main()
