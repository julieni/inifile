<?php

/**
 * @author     Laurent Jouanneau
 * @copyright  2008-2015 Laurent Jouanneau
 *
 * @link       http://jelix.org
 * @licence    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

namespace Jelix\IniFile;

/**
 * utility class to read and modify two ini files at the same time :
 * one master file, and one file which overrides values of the master file,
 * like we have in jelix with mainconfig.ini.php and config.ini.php of an entry point.
 */
class MultiIniModifier
{
    /**
     * @var \Jelix\IniFile\IniModifier
     */
    protected $master;

    /**
     * @var \Jelix\IniFile\IniModifier
     */
    protected $overrider;

    /**
     * load the two ini files.
     *
     * @param \Jelix\IniFile\IniModifier|string $master    the master ini file (object or filename)
     * @param \Jelix\IniFile\IniModifier|string $overrider the ini file overriding the master ini file (object or filename)
     */
    public function __construct($master, $overrider)
    {
        if (is_object($master)) {
            $this->master = $master;
        } else {
            $this->master = new IniModifier($master);
        }
        if (is_object($overrider)) {
            $this->overrider = $overrider;
        } else {
            $this->overrider = new IniModifier($overrider);
        }
    }

    /**
     * modify an option in the ini file. If the option doesn't exist,
     * it is created.
     *
     * @param string $name    the name of the option to modify
     * @param string $value   the new value
     * @param string $section the section where to set the item. 0 is the global section
     * @param string $key     for option which is an item of array, the key in the array
     * @param bool   $master  if true, change the value in the master file, else change
     *                        the value in the overrider file (default)
     */
    public function setValue($name, $value, $section = 0, $key = null, $master = false)
    {
        if ($master) {
            $this->master->setValue($name, $value, $section, $key);
        } else {
            $this->overrider->setValue($name, $value, $section, $key);
        }
    }

    /**
     * return the value of an option from the ini files. If the option doesn't exist,
     * it returns null.
     *
     * @param string $name       the name of the option to retrieve
     * @param string $section    the section where the option is. 0 is the global section
     * @param string $key        for option which is an item of array, the key in the array
     * @param bool   $masterOnly if true, get the value from the master file, else
     *                           get the value from the overrider file or from the master file
     *                           if the value doesn't exists in the overrider file (default)
     *
     * @return mixed the value
     */
    public function getValue($name, $section = 0, $key = null, $masterOnly = false)
    {
        if ($masterOnly) {
            return $this->master->getValue($name, $section, $key);
        } else {
            $val = $this->overrider->getValue($name, $section, $key);
            if ($val === null) {
                $val = $this->master->getValue($name, $section, $key);
            }

            return $val;
        }
    }

    /**
     * save the ini files.
     */
    public function save($chmod = null)
    {
        $this->master->save($chmod);
        $this->overrider->save($chmod);
    }

    /**
     * says if the ini content has been modified.
     *
     * @return bool
     *
     * @since 1.2
     */
    public function isModified()
    {
        return $this->master->isModified() || $this->overrider->isModified();
    }

    /**
     * @return \Jelix\IniFile\IniModifier the first ini file
     *
     * @since 1.2
     */
    public function getMaster()
    {
        return $this->master;
    }

    /**
     * @return \Jelix\IniFile\IniModifier the second ini file
     *
     * @since 1.2
     */
    public function getOverrider()
    {
        return $this->overrider;
    }
}
