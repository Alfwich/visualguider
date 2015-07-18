import os, sys

def transformName(name):
  return "".join([ i for i in name.replace(u" ","-") if i.isalnum() or i == "-" ])

def main():
  for f in os.listdir("."):
    if f[-3:] == "jpg":
      newName = transformName(f[:-3])
      os.system( "mv '%s' '%s'" % ( f, "%s.jpg" % newName ) )

if __name__ == "__main__":
  main()
