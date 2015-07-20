# Quick script to format old card image names to the new format

import os, sys

def transformName(name):
  return "%s.jpg" % "".join([ i for i in name.replace(u" ","-") if i.isalnum() or i == "-" ])

def main():
  totalRenames = 0
  for f in os.listdir("."):
    if f[-3:] in "jpg":
      newName = transformName(f[:-3])

      # Move the file if the name has changed
      if not f == newName:
        print( "renaming image '%s' => '%s'" % (f, newName) )
        totalRenames += 1
        os.system( "mv '%s' '%s'" % ( f, newName ) )

  print( "renamed a total of %d cards" % totalRenames )
if __name__ == "__main__":
  main()
