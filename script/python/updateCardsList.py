import MySQLdb, sys, os
import generateCardsList as GCL

def main():
  GCL.main()
  with open("cards.sql","r") as f:
    print( "Inserting into database..." )
    con = MySQLdb.connect( "localhost", "arthurwut", "", "visualguider" )
    con.query( f.read() )
    print( "Done." )


if __name__ == "__main__":
  main()

