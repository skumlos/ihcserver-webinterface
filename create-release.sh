#!/bin/sh
RLSNAME=ihcserver-webinterface

if [ "$#" -lt "1" ]; then
        echo "Usage $0 <version>";
        exit 1;
fi

echo "Creating $RLSNAME-$1.tar.gz";

tar -czf $RLSNAME-$1.tar.gz . --exclude='$0' --exclude='*.gz' --exclude='*git*'

