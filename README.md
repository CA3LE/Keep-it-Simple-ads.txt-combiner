# combine local and remote ads.txt files, only if updated.

# Keep it Simple ads.txt combiner by Damon @ TestMy.net 2020

# This script will check freestar's ads.txt, compare the date to your local ads.txt and update only if needed.
# It will also combine your site's custom ads.txt lines with freestar's master ads.txt.  
# IMPORTANT: Make sure your server ALWAYS has disk space for the write... otherwise you may end up with a zero byte ads.txt file, obviously not ideal.


// Make cronjob to execute every 10 minutes. 
