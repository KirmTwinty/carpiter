#!/bin/sh
PROGNAME=$(basename $0)
BIN_PATH=./src
INSTALLATION_PATH=./installation

error_exit(){
    #   ----------------------------------------------------------------
    #   Function for exit due to fatal program error
    #       Accepts 1 argument:
    #           string containing descriptive error message
    #   ----------------------------------------------------------------
    echo "${PROGNAME}: ${1:-"Unknown Error"}" 1>&2
    exit 1
}
warning_exit(){
    #   ----------------------------------------------------------------
    #   Function for exit due to fatal program error
    #       Accepts 1 argument:
    #           string containing descriptive error message
    #   ----------------------------------------------------------------
    echo "${PROGNAME}: ${1:-"Unknown Warning"}" 1>&2
    exit 0
}

check_program(){
    #   ----------------------------------------------------------------
    #   Function to check whether a program is installed or not    
    #           1: string of the command to check the installation
    #           2: string of the program name
    #           3: string of the command to perform the installation
    #   example: mysql MySQL mysql-server
    #   ----------------------------------------------------------------
    type $1 >/dev/null 2>&1 && bool=true || bool=false
    if $bool ; then
	echo "0"
    else
	read -p "  $2 not installed, do you want to install it (y/n)?" choice
	case "$choice" in 
	    y|Y ) echo "  Running sudo apt-get install $1"; sudo apt-get install $1;;
	    n|N ) error_exit "You have to install $1 to continue (sudo apt-get install $1)";;
	    * ) error_exit "You have to install $1 to continue (sudo apt-get install $1)";;
	esac
	echo "1"
    fi
}
# file_copy(){
#     path=$1
#     for f in $path; do       	
# 	if [ -d $f ]; then 
# 	    file_copy "$f/*" $2
# 	else
# 	    echo "Installing $f..."
# 	    cp $f $2
# 	fi
#     done
# } 
# Check the configuration
echo "********************************************************"
echo "************ Check the current installation ************"
echo "********************************************************"

echo "    Checking for Apache Installation..."
bApache=$(check_program apache2 Apache apache2)
echo "    Apache OK"

echo "    Checking for PHP Installation..."
bPhp=$(check_program php PHP php5)
echo "    PHP OK"

echo "    Checking for MySQL Installation..."
bMysql=$(check_program mysql MySQL mysql-server)
echo "    MySQL OK"

needReboot=$((bApache | bPhp | bMysql))

echo " "

if [ "$needReboot" -eq "1" ] ; then
    echo "Please reboot and then launch the script again. "
    read -p "Reboot now (y/n)?" choice
    case "$choice" in 
	y|Y ) echo "  > Running sudo reboot"; sudo reboot;;
	n|N ) warning_exit " > Don't forget to reboot before running the script";;
	* ) warning_exit " > Don't forget to reboot before running the script";;
    esac
else
    echo "********************************************************"
    echo "***************** Deploying application ****************"
    echo "********************************************************"
    cp -Rv "$BIN_PATH/" "$INSTALLATION_PATH/"
    mv  $INSTALLATION_PATH/src/* $INSTALLATION_PATH/
    rm -R $INSTALLATION_PATH/src/
    echo " "
    echo "********************************************************"
    echo "******************** Create Database  ******************"
    echo "********************************************************"
    
fi

