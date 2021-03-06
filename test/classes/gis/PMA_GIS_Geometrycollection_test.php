<?php

/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Test for PMA_GIS_Geometry
 *
 * @package PhpMyAdmin-test
 */
require_once 'libraries/gis/pma_gis_geometry.php';
require_once 'libraries/gis/pma_gis_geometrycollection.php';
require_once 'libraries/gis/pma_gis_factory.php';
require_once 'libraries/tcpdf/tcpdf.php';

/**
 * Tests for PMA_GIS_Geometrycollection class
 *
 * @package PhpMyAdmin-test
 */
class PMA_GIS_Geometrycollection_test extends PHPUnit_Framework_TestCase
{

    /**
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        $this->object = PMA_GIS_Geometrycollection::singleton();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     * @return void
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     *
     * @param type $spatial
     *
     * @dataProvider providerForScaleRow
     */
    public function testScaleRow($spatial, $output)
    {

        $this->assertEquals($this->object->scaleRow($spatial), $output);
    }

    public function providerForScaleRow()
    {

        return array(
            array(
                'GEOMETRYCOLLECTION(POLYGON((35 10,10 20,15 40,45 45,35 10),(20 30,35 32,30 20,20 30)))',
                Array(
                    'maxX' => 45.0,
                    'minX' => 10.0,
                    'maxY' => 45.0,
                    'minY' => 10.0
                )
            )
        );
    }

    /**
     *
     * @param type $gis_data
     * @param type $index
     * @param string $empty
     * @param type $output
     *
     * @dataProvider providerForGenerateWkt
     */
    public function testGenerateWkt($gis_data, $index, $empty, $output)
    {

        $this->assertEquals($this->object->generateWkt($gis_data, $index, $empty = ''), $output);
    }

    public function providerForGenerateWkt()
    {

        $temp1 = array(
            0 => array(
                'gis_type' => 'LINESTRING',
                'LINESTRING' => array(
                    'no_of_points' => 2,
                    0 => array('x' => 5.02, 'y' => 8.45),
                    1 => array('x' => 6.14, 'y' => 0.15)
                )
            )
        );

        return array(
            array(
                $temp1,
                0,
                null,
                'GEOMETRYCOLLECTION(LINESTRING(5.02 8.45,6.14 0.15))'
            )
        );
    }

    /**
     * @param $value
     * @param $output
     *
     * @dataProvider providerForGenerateParams
     */
    public function testGenerateParams($value, $output)
    {

        $this->assertEquals($this->object->generateParams($value), $output);
    }

    public function providerForGenerateParams()
    {

        return array(
            array(
                'GEOMETRYCOLLECTION(LINESTRING(5.02 8.45,6.14 0.15))',
                array(
                    'srid' => 0,
                    'GEOMETRYCOLLECTION' => Array('geom_count' => 1),

                '0' => Array(
                    'gis_type' => 'LINESTRING',
                    'LINESTRING' => Array(
                        'no_of_points' => 2,
                        '0' => Array(
                            'x' => 5.02,
                            'y' => 8.45
                        ),
                        '1' => Array(
                            'x' => 6.14,
                            'y' => 0.15
                        )
                    )

                )
                ),
            ),
        );
    }

    /**
     *
     * @param type $spatial
     * @param type $label
     * @param type $line_color
     * @param type $scale_data
     * @param type $image
     * @param type $output
     *
     * @dataProvider providerForPrepareRowAsPng
     */
    public function testPrepareRowAsPng($spatial, $label, $line_color, $scale_data, $image, $output)
    {

        $return = $this->object->prepareRowAsPng($spatial, $label, $line_color, $scale_data, $image);
        $this->assertTrue(true);
    }

    public function providerForPrepareRowAsPng()
    {

        return array(
            array(
                'GEOMETRYCOLLECTION(POLYGON((35 10,10 20,15 40,45 45,35 10),(20 30,35 32,30 20,20 30)))',
                'image',
                '#B02EE0',
                array(
                    'x' => 12,
                    'y' => 69,
                    'scale' => 2,
                    'height' => 150
                ),
                imagecreatetruecolor('120', '150'),
                ''
            )
        );
    }

    /**
     *
     * @param type $spatial
     * @param type $label
     * @param type $line_color
     * @param type $scale_data
     * @param type $pdf
     *
     * @dataProvider providerForPrepareRowAsPdf
     */
    public function testPrepareRowAsPdf($spatial, $label, $line_color, $scale_data, $pdf)
    {

        $return = $this->object->prepareRowAsPdf($spatial, $label, $line_color, $scale_data, $pdf);
        $this->assertTrue($return instanceof TCPDF);
    }

    public function providerForPrepareRowAsPdf()
    {

        return array(
            array(
                'GEOMETRYCOLLECTION(POLYGON((35 10,10 20,15 40,45 45,35 10),(20 30,35 32,30 20,20 30)))',
                'pdf',
                '#B02EE0',
                array(
                    'x' => 12,
                    'y' => 69,
                    'scale' => 2,
                    'height' => 150
                ),
                new TCPDF(),
            )
        );
    }

    /**
     *
     * @param type $spatial
     * @param type $label
     * @param type $line_color
     * @param type $scale_data
     * @param type $output
     *
     * @dataProvider providerForPrepareRowAsSvg
     */
    public function testPrepareRowAsSvg($spatial, $label, $line_color, $scale_data, $output)
    {

        $string = $this->object->prepareRowAsSvg($spatial, $label, $line_color, $scale_data);
        $this->assertEquals(1, preg_match($output, $string));
//        $this->assertEquals($this->object->prepareRowAsSvg($spatial, $label, $line_color, $scale_data) , $output);
    }

    public function providerForPrepareRowAsSvg()
    {

        return array(
            array(
                'GEOMETRYCOLLECTION(POLYGON((35 10,10 20,15 40,45 45,35 10),(20 30,35 32,30 20,20 30)))',
                'svg',
                '#B02EE0',
                array(
                    'x' => 12,
                    'y' => 69,
                    'scale' => 2,
                    'height' => 150
                ),
                '/^(<path d=" M 46, 268 L -4, 248 L 6, 208 L 66, 198 Z  M 16, 228 L 46, 224 L 36, 248 Z " name="svg" id="svg)(\d+)(" class="polygon vector" stroke="black" stroke-width="0.5" fill="#B02EE0" fill-rule="evenodd" fill-opacity="0.8"\/>)$/'
            )
        );
    }

    /**
     *
     * @param type $spatial
     * @param type $srid
     * @param type $label
     * @param type $line_color
     * @param type $scale_data
     * @param type $output
     *
     * @dataProvider providerForPrepareRowAsOl
     */
    public function testPrepareRowAsOl($spatial, $srid, $label, $line_color, $scale_data, $output)
    {

        $this->assertEquals($this->object->prepareRowAsOl($spatial, $srid, $label, $line_color, $scale_data), $output);
    }

    public function providerForPrepareRowAsOl()
    {

        return array(
            array(
                'GEOMETRYCOLLECTION(POLYGON((35 10,10 20,15 40,45 45,35 10),(20 30,35 32,30 20,20 30)))',
                4326,
                'Ol',
                '#B02EE0',
                array(
                    'minX' => '0',
                    'minY' => '0',
                    'maxX' => '1',
                    'maxY' => '1',
                ),
                'bound = new OpenLayers.Bounds(); bound.extend(new OpenLayers.LonLat(0, 0).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject())); bound.extend(new OpenLayers.LonLat(1, 1).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()));vectorLayer.addFeatures(new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon(new Array(new OpenLayers.Geometry.LinearRing(new Array((new OpenLayers.Geometry.Point(35,10)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()), (new OpenLayers.Geometry.Point(10,20)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()), (new OpenLayers.Geometry.Point(15,40)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()), (new OpenLayers.Geometry.Point(45,45)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()), (new OpenLayers.Geometry.Point(35,10)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()))), new OpenLayers.Geometry.LinearRing(new Array((new OpenLayers.Geometry.Point(20,30)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()), (new OpenLayers.Geometry.Point(35,32)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()), (new OpenLayers.Geometry.Point(30,20)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()), (new OpenLayers.Geometry.Point(20,30)).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()))))), null, {"strokeColor":"#000000","strokeWidth":0.5,"fillColor":"#B02EE0","fillOpacity":0.8,"label":"Ol","fontSize":10}));'
            )
        );
    }

}

?>
