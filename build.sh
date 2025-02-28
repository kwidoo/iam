export APP_ENV=stage
if [ "${DRONE_BRANCH}" = "master" ]; then
    export APP_ENV=master
fi

docker build --build-arg APP_ENV=${APP_ENV} --build-arg TKN=${TKN} -t iam:${DRONE_COMMIT_SHA} -f Dockerfile .
docker tag one-c-bridge:${DRONE_COMMIT_SHA} iam:${DRONE_COMMIT_SHA}
docker push iam:${DRONE_COMMIT_SHA}
