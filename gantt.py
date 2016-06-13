from random import randint;
from copy import copy;
import sys;
from collections import namedtuple;

Process = namedtuple("Process","pid, entered, burst, priority");
GanttElem = namedtuple("GanttElem","pid, timestamp, burst");

def genProcs( numProcess ):
	l = 0
	procs = [Process(None,None,None,None)]*numProcess
	for i in range(0,numProcess):
		procs[i] = Process(i+1,l,randint(1,32),randint(1,32))
		l = l + randint(0,5)
		print >>procfile,'<tr>'
		print >>procfile,'<td id=procCell>',procs[i].pid,'</td>'
		print >>procfile,'<td id=procCell>',procs[i].entered,'</td>'
		print >>procfile,'<td id=procCell>',procs[i].burst,'</td>'
		print >>procfile,'<td id=procCell>',procs[i].priority,'</td>'
		print >>procfile,'</tr>'
	return procs;

#runs First Come First Serve
def fcfs(procs):
	gantt = [GanttElem(None,None,None)]*len(procs)
	ID = 0;
	TS = 0;
	BT = 0;
	for p in procs:
		BT = p.burst;
		if TS < p.entered:
			TS = p.entered
		gantt[ID] = GanttElem(ID,TS,BT)
		TS = TS + BT
		ID = ID + 1
		print >>prcfcfs,'<div id=gantt style="width:'+str(5*BT+25)+'px;">P' \
		+str(ID)+'<div id=ganttLeft>'+str(TS-BT)+'+'+str(BT)+'</div></div>'

#Updates flags for processes entering the queue for SJF
def sjf_enter(procs,ts):
	maxim = True;
	for p in procs:
		if( p.entered <= ts and p.priority >= 0 ):
			procs[p.pid-1] = Process(p.pid,ts,p.burst,0);
		if( p.entered > ts and p.priority > 0 ):
			return False
		elif( p.priority >= 0 ):
			maxim = False
	return maxim

#Selects the process with the shortest burst time
def sjf_select_proc(procs):
	best = Process(-1,-1,100,-1)
	for p in procs:
		if( p.priority == 0 and p.burst < best.burst ):
			best = p
		if( p.priority > 0 ):
			break
	return best

#Selects the process with the shortest burst time that might prempt the
#currently running process
def sjf_find_next(curr,procs):
	best = curr;
	for p in procs:
		if( p.priority > 0 ):
			if( best.pid != curr.pid and p.entered > best.entered ):
				return best
			if( best.burst - p.entered > p.burst - best.entered  ):
				best = p
	return best

#Runs the full SJF and prints the html for the gantt chart to the file
def sjf(procs):
	gantt = []
	TMP = 0;
	PRE = False;
	BT = 0;
	TS = 0;
	p = Process(0,0,0,0);
	while( not sjf_enter(procs,TS) ):
		#pick next process if undecided
		if(not PRE):
			p = sjf_select_proc(procs)
		ID = p.pid
		TS = p.entered
		#check for a preempting program
		n = sjf_find_next(p,procs)
		TMP = n.entered - p.entered
		#check if we found a preempting program
		if( TMP == 0 or TMP >= p.burst ):
			BT = p.burst
			procs[p.pid-1] = Process(p.pid,p.entered,p.burst,-1)
			PRE = False
		else:
			BT = TMP
			PRE = True
		#Append Result
		gantt.append(GanttElem(ID,TS,BT))
		print >>prcsjf,'<div id=gantt style="width:'+str(5*BT+25)+'px;">P' \
		+str(ID)+'<div id=ganttLeft>'+str(TS)+'+'+str(BT)+'</div></div>'
		TS = TS + BT
		if( PRE ):
			procs[p.pid-1] = Process(p.pid,p.entered,p.burst-BT,0)
			p = n

#This funtion checks if all processes have completed in Round Robin
def rr_done(procs):
	for p in procs:
		if(p.burst > 0):
			return False
	return True

#This function does round robin, in a rather slow and bad way, but does it
#none the less
def rr(procs,quanta):
	gantt = []
	ID = 0
	TS = 0
	BT = 0
	while( not rr_done(procs) ):
		for p in procs:
			if( p.burst > 0 ):
				if( p.burst >= quanta ):
					gantt.append(GanttElem(p.pid,TS,quanta))
					print >>prcrr,'<div id=gantt style="width:'+str(5*quanta+25)+'px;">P' \
					+str(p.pid)+'<div id=ganttLeft>'+str(TS)+'+'+str(quanta)+'</div></div>'
					TS = TS + quanta
					procs[p.pid-1] = Process(p.pid,p.entered,p.burst-quanta,p.priority)
				else:
					gantt.append(GanttElem(p.pid,TS,p.burst))
					print >>prcrr,'<div id=gantt style="width:'+str(5*p.burst+25)+'px;">P' \
					+str(p.pid)+'<div id=ganttLeft>'+str(TS)+'+'+str(p.burst)+'</div></div>'
					TS = TS + p.burst
					procs[p.pid-1] = Process(p.pid,p.entered,0,p.priority)

#This function finds the process with the next highest priority that might
#preempt the currently running process
def pri_find_next(procs,init,ends,pid,pri):
	best = Process(-1,ends,100,pri)
	for p in procs:
		if( p.entered < init ):
			procs[p.pid-1] = Process(p.pid,init,p.burst,p.priority)
	for p in procs:
		if( p.burst > 0 ):
			if( p.entered <= best.entered and p.entered <= ends and p.priority < best.priority):
				best = p
			elif( p.entered > best.entered or p.entered > ends ):
				return best
	return best

#This function runs the priority scheduler and prints the gantt chart to a file
def pri(procs):
	gantt=[]
	prev = Process(-1,0,0,100)
	TS = 0
	BT = 0
	while( not rr_done(procs) ):
		curr = pri_find_next(procs,prev.entered,prev.entered+prev.burst,prev.pid,prev.priority)
		if( curr.pid == -1 ):
			gantt.append(GanttElem(prev.pid,prev.entered,prev.burst))
			print >>prcpri,'<div id=gantt style="width:'+str(5*prev.burst+25)+'px;">P' \
			+str(prev.pid)+'<div id=ganttLeft>'+str(prev.entered)+'+'+str(prev.burst)+'</div></div>'
			TS = prev.entered+prev.burst
			procs[prev.pid-1] = Process(prev.pid,prev.entered,0,prev.priority)
			prev = Process(-1,TS,100,100)
		elif( prev.pid != -1 ):
			BT = curr.entered-prev.entered
			gantt.append(GanttElem(prev.pid,prev.entered,BT))
			print >>prcpri,'<div id=gantt style="width:'+str(5*BT+25)+'px;">P' \
			+str(prev.pid)+'<div id=ganttLeft>'+str(prev.entered)+'+'+str(BT)+'</div></div>'
			TS = prev.entered + BT
			procs[prev.pid-1] = Process(prev.pid,prev.entered,prev.burst-BT,prev.priority)
			prev = Process(curr.pid,TS,curr.burst,curr.priority)
		else:
			prev = curr;

#open files to store gantt charts
procfile = open("procs.txt","w+")
prcfcfs = open("prcfcfs.txt","w+")
prcsjf = open("prcsjf.txt","w+")
prcrr = open("prcrr.txt","w+")
prcpri = open("prcpri.txt","w+")

#Do all the necessary actions
N = (int)(sys.argv[1])
TQ = (int)(sys.argv[2])
a = genProcs(N)
b = copy(a)
c = copy(a)
d = copy(a)
fcfs(a)
sjf(b)
rr(c,TQ)
pri(d)


