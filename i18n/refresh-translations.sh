# Grab translatable strings from sources
# merge them into teke-php.pot
# Note: this requires Python (2.6 or later) + i18ndude installed in the system
# If you already have python in the system just run: sudo easy_install i18ndude
# Note: in order to use easy_install python needs setuptools to be present
# More information can be found here: http://pypi.python.org/pypi/setuptools 

# Output initial number of translation strings if available
if [ -f teke.pot ]
then
	echo "Translations in teke.pot:" $(( $(cat teke.pot|grep 'msgid'|wc -l) - 1 ))
fi

#Note: all .php files in the project are scanned for translation strings
find .. -path "../includes" -prune -o -iname "*.php" -print | xargs xgettext --default-domain=teke --language=PHP --output=teke-php.pot
if [ -r teke-php.pot ]
then
	echo "Standard .php files processed ok:" $(( $(cat teke-php.pot|grep 'msgid'|wc -l) - 1 ))
else
	echo "Problem processing standard .php files!" && exit 1
fi

#Write pickup for teke-templates.pot
i18ndude rebuild-pot --pot teke-templates.pot --create teke ../views/
if [ -r teke-templates.pot ]
then
	echo "Templates processed ok:" $(( $(cat teke-templates.pot|grep 'msgid'|wc -l) - 1 ))
else
	echo "Problem processing templates!" && exit 1
fi

echo "Translations is manual.pot:" $(( $(cat manual.pot|grep 'msgid'|wc -l) - 1 ))
#Merge all pots into main teke.pot
msgcat --use-first --to-code=UTF-8 teke-php.pot teke-templates.pot manual.pot teke.pot > teke.pot

#Filter out unneded translation strings
echo "Translations in ignored.pot:" $(( $(cat ignored.pot|grep 'msgid'|wc -l) - 1 ))
i18ndude filter teke.pot ignored.pot > filtered.pot
mv filtered.pot teke.pot

# Remove unneeded .pot files
if [ -f teke-php.pot ]
then
	rm teke-php.pot
fi
if [ -f teke-templates.pot ]
then
	rm teke-templates.pot
fi

# Output final number of translations
echo "Translations in teke.pot:" $(( $(cat teke.pot|grep 'msgid'|wc -l) - 1 ))

# Merge the new strings from teke.pot into locale-specific .po files
# ENG
msgmerge en_GB.UTF-8/LC_MESSAGES/teke.po teke.pot > new.po
mv new.po en_GB.UTF-8/LC_MESSAGES/teke.po
# EST
msgmerge et_EE.UTF-8/LC_MESSAGES/teke.po teke.pot > new.po
mv new.po et_EE.UTF-8/LC_MESSAGES/teke.po
# RUS
msgmerge ru_RU.UTF-8/LC_MESSAGES/teke.po teke.pot > new.po
mv new.po ru_RU.UTF-8/LC_MESSAGES/teke.po
# FIN
msgmerge fi_FI.UTF-8/LC_MESSAGES/teke.po teke.pot > new.po
mv new.po fi_FI.UTF-8/LC_MESSAGES/teke.po
# SWE
msgmerge sv_SE.UTF-8/LC_MESSAGES/teke.po teke.pot > new.po
mv new.po sv_SE.UTF-8/LC_MESSAGES/teke.po

echo "Done"
