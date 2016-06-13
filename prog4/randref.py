from random import randint, shuffle
import sys

N = (int)(sys.argv[1])
F = (int)(sys.argv[2])

frames = range(0,F)
pages = range(0,N)
shuffle(frames)

L = min(len(frames),len(pages))

for i in range(0,L):
	print pages[i]
	print frames[i]
	print randint(0,1023)



