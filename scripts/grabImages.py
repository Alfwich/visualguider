import urllib2
import re

# open site
site = urllib2.urlopen('https://www.wizards.com/magic/tcg/article.aspx?x=mtg/tcg/riseoftheeldrazi/spoiler')

# create list of relative image urls
image_list = re.findall(r"/mtg/images/tcg/products/riseoftheeldrazi/rdfgh8rvhs/EN/\d{4}_MTGROE_EN_LR.jpg", site.read())

