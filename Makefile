###
###
.PHONY: nagvis nagios clean
HOST=$(shell hostname)

ifeq ($(HOST),ctrl-dev)
   nagioscfg=nagios-hosts
else
   nagioscfg=
endif

all: clean

clean:
	rm -f test-write-xls.xls test-write-xls.xlsx
