# -*- coding: utf-8 -*-

import tagtool as TT
import cli
import argparse
import sys

myclass = TT

if __name__ == '__main__':
    try:
        TT.TagTool().run()
    except TT.ArgparseError as e:
        _prog = e.args[1].split()
    else:
        sys.exit(0)

    if len(_prog) == 1:
        sys.argv = sys.argv + ['show']
    elif _prog[-1] == "format":
        sys.argv = sys.argv + ['any']

    try:
        hoge=TT.TagTool()
        test=hoge.run()
        #print(test)
        #print(hoge.rdwr_commands["show"])
    except TT.ArgparseError as e:
        print(e, file=sys.stderr)


