LIMA_NAME=docker-vm
limactl stop -f $LIMA_NAME
limactl delete $LIMA_NAME
limactl start --name=$LIMA_NAME template://docker
limactl stop $LIMA_NAME
cp -f ./lima.yaml ~/.lima/$LIMA_NAME/lima.yaml
limactl start $LIMA_NAME
docker context create lima-$LIMA_NAME --docker "host=unix://~/.lima/$LIMA_NAME/sock/docker.sock"
docker context use lima-$LIMA_NAME