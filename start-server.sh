#!/bin/bash

# start the virtual machine with the server
vagrant up

# forward port 8000 to 80 inside the virtual machine (will auto close when user runs `vagrant halt`)
(vagrant ssh -c 'socat tcp-listen:8000,reuseaddr,fork tcp:localhost:80') &