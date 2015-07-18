import urllib2, json, os, sys

def transformName(name):
  return "".join([ i for i in name.replace(u" ","-") if i.isalnum() or i == "-" ])

def get(d,k):
  result = u""
  
  if k in d:
    result = unicode(d[k]).replace( u"\"", u"'" )
    
  return result

def checkImageExists(name):
  name = transformName( name )
  if os.path.isfile( "/home/webserver/www/visualguider/image/card/%s.jpg" % ( name ) ):
    sys.stdout.write( " *** FOUND CACHED IMAGE..." )
    return (u"%s"%name)
  return u""

def main():
  with open( "cards.sql", "w") as f:
    print( "Getting MTG card list from endpoint 'http://mtgjson.com/json/AllSets.json' ..." )
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
    print( "Done." )
    print( "Creating SQL." )
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

      
      sys.stdout.write( u"Processing card: {0}...".format( get(card,"name")[:20] ) )
      entry = unicode( u" (\"{0}\",\"{1}\",\"{2}\",\"{3}\",\"{4}\",\"{5}\")\n" ).format( 
          get(card,"name"), 
          u"MTG", 
          get(card,"manaCost"), 
          get(card,"set"), 
          rarityMap[get(card,"rarity")], 
          checkImageExists( get(card,"name") ) )

      print( "done." )
        
      f.write(entry.encode('utf8'))
    f.write( ";" )
    print( "Finished creating card sql." )


if __name__ == "__main__":
  main()
